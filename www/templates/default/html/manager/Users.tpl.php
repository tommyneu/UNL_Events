<?php
	$crumbs = new stdClass;
	$crumbs->crumbs = array(
		"Events Manager" => "/manager",
		$context->calendar->name => $context->calendar->getManageURL(),
		"Users & Permissions" => NULL
	);
	echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
	//$calendar = Calendar::getByShortname($this->options['calendar_shortname']);
?>

<div class="dcf-d-flex dcf-flex-wrap dcf-flex-row dcf-jc-between dcf-ai-center dcf-mb-6 dcf-row-gap-3">
    <h1 class="dcf-m-0">Users on this Calendar</h1>
    <a class="dcf-btn dcf-btn-primary" href="<?php echo $base_manager_url . $context->calendar->shortname ?>/users/new/">Add User</a>
</div>

<ul role="list" class="dcf-list-inline dcf-mb-6 dcf-d-flex dcf-flex-wrap dcf-flex-row dcf-gap-6" id="userList">
    <?php foreach($context->getUsers() as $user): ?>
        <li class="dcf-rounded dcf-relative unl-bg-lightest-gray dcf-m-0 dcf-p-0 dcf-mt-1">
            <?php
                $fullName = $user->getFullName();
                $profile_image_src = "https://directory.unl.edu/avatar/" . $user->uid . "/?s=800";
                $directory_link = "https://directory.unl.edu/people/" . $user->uid;
                if ($fullName === false) {
                    $profile_image_src = "https://directory.unl.edu/images/default-avatar-800.jpg";
                }
            ?>
            <div class="userList_profile_image dcf-ratio dcf-ratio-1x1 dcf-w-auto dcf-b-solid dcf-b-1 unl-b-light-gray dcf-m-2">
                <img class="dcf-ratio-child dcf-obj-fit-cover" src="<?php echo $profile_image_src; ?>">
            </div>
            <p class="userList_name dcf-txt-xs dcf-txt-center dcf-p-2 dcf-pt-0 dcf-m-0 dcf-lh-1">
                <?php if ($fullName !== false): ?>
                    <span> <?php echo $fullName; ?></span>
                <?php else: ?>
                    <span class="dcf-d-inline-block unl-bg-scarlet unl-cream dcf-rounded dcf-p-1">Unknown User!</span>
                <?php endif; ?>
                <a class="uid dcf-d-block dcf-txt-xs dcf-pt-1" href="<?php echo $directory_link; ?>" target="_blank">
                    <?php echo $user->uid; ?>
                </a>
            </p>
            <p class="userList_edit_permissions dcf-p-0 dcf-m-0 dcf-mb-3 dcf-txt-center">
                <a class="dcf-btn dcf-btn-secondary dcf-txt-xs" href="<?php echo $user->getEditPermissionsURL($context->calendar); ?>">Edit</a>
            </p>
            <div class="userList_delete_form dcf-absolute dcf-top-0 dcf-right-0">
                <form method="post" action="<?php echo $user->getDeletePermissionsURL($context->calendar); ?>" class="dcf-form delete-form">
                    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey(); ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName(); ?>" />
                    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey(); ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue(); ?>">
                    <input type="hidden" name="user_uid" value="<?php echo $user->uid; ?>" />
                    <button class="dcf-btn dcf-btn-primary dcf-circle dcf-h-6 dcf-w-6 dcf-p-0 dcf-m-0 dcf-d-flex dcf-jc-center dcf-ai-center" type="submit" value="Remove" title="Remove">
                        <svg class="dcf-h-4 dcf-w-4 dcf-fill-current dcf-d-block"
                            width="24" height="24" viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                        <path style="rotate: 45deg; transform-origin: center center;"
                            d="M1,13h22c0.6,0,1-0.4,1-1c0-0.6-0.4-1-1-1H1c-0.6,0-1,0.4-1,1C0,12.6,0.4,13,1,13z"/>
                        <path style="rotate: -45deg; transform-origin: center center;"
                            d="M1,13h22c0.6,0,1-0.4,1-1c0-0.6-0.4-1-1-1H1c-0.6,0-1,0.4-1,1C0,12.6,0.4,13,1,13z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
