<?php
    const CHECKED_INPUT = 'checked="checked"';
    $query = strtolower($context->search_query ?? "");
?>
<div class="dcf-grid dcf-col-gap-vw">
    <section class="dcf-col-100% dcf-col-33%-start@md">
        <nav>
            <a class="dcf-show-on-focus" href="#results">Skip filters</a>
        </nav>
        <h2 class="dcf-txt-h4">Filter Results</h2>
        <form id="audience_form" class="dcf-form dcf-mt-5">
            <?php $all_audiences = $context->getAudiences(); ?>

            <input type="hidden" id="hidden_query" name="q" value="<?php echo $context->search_query ?? ""; ?>">

            <fieldset>
                <legend>Target Audiences</legend>
                <?php foreach ($all_audiences as $single_audience) : ?>
                    <?php
                        $target_audience_id = 'target-audience-' . $single_audience->id;
                        $in_query = strpos($query, strtolower($single_audience->name));
                    ?>
                    <div class="dcf-input-checkbox">
                        <input
                            id="<?php echo $target_audience_id; ?>"
                            type="checkbox"
                            value="<?php echo $single_audience->name; ?>"
                            <?php
                                if ($in_query !== false) {
                                    echo CHECKED_INPUT;
                                }
                            ?>
                        >
                        <label for="<?php echo $target_audience_id; ?>">
                            <?php echo $single_audience->name; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </fieldset>
        </form>

        <script>
            const form = document.getElementById('audience_form');
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            const hidden_query = document.getElementById('hidden_query');

            // Submit if select changes
            checkboxes.forEach((input) => {
                input.addEventListener('input', () => {
                    const checkedCheckboxes = form.querySelectorAll('input[type="checkbox"]:checked');
                    hidden_query.value = Array.from(checkedCheckboxes).map((checkbox) => checkbox.value).join(", ");
                    if (checkedCheckboxes.length > 0) {
                        form.submit();
                    }
                });
            });
        </script>
    </section>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/Audience.tpl.php'); ?>

        <?php
            $previous_offset = $context->offset - $context->limit;
            $next_offset     = $context->offset + $context->limit;

            $previous_link = $context->getURL();
            $next_link     = $context->getURL();

            if ($context->limit != 100) {
                $previous_link .= '&limit=' . $context->limit;
                $next_link     .= '&limit=' . $context->limit;
            }

            if ($previous_offset > 0) {
                $previous_link .= '&offset=' . $previous_offset;
            }

            if ($next_offset < $context->count()) {
                $next_link .= '&offset=' . $next_offset;
            }
        ?>

        <?php if($context->count() > 0 && !($previous_offset < 0 && $next_offset > $context->count())): ?>
            <div class="dcf-d-flex dcf-flex-row dcf-flex-nowrap dcf-jc-between dcf-ai-end dcf-mt-3">
                <?php if ($previous_offset < 0): ?>
                    <?php // We wanted to be able to disable this but you can not disable a link ?>
                    <button
                        class="dcf-btn dcf-btn-secondary"
                        disabled
                    >
                        Previous <?php echo $context->limit; ?>
                    </button>
                <?php else: ?>
                    <a
                        class="dcf-btn dcf-btn-secondary"
                        href="<?php echo $previous_link; ?>"
                    >
                        Previous <?php echo $context->limit; ?>
                    </a>
                <?php endif; ?>

                <p class="dcf-txt-xs">Only Displaying <?php echo $context->limit; ?> Results at a time</p>

                <?php if ($next_offset > $context->count()): ?>
                    <?php // We wanted to be able to disable this but you can not disable a link ?>
                    <button
                        class="dcf-btn dcf-btn-secondary"
                        disabled
                    >
                        Next <?php echo $context->limit; ?>
                    </button>
                <?php else: ?>
                    <a
                        class="dcf-btn dcf-btn-secondary"
                        href="<?php echo $next_link; ?>"
                    >
                        Next <?php echo $context->limit; ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
