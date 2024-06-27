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

<h1>Users on this Calendar</h1>
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
            <img class="userList_profile_image dcf-p-2 dcf-w-max-100% dcf-h-max-100% dcf-w-auto dcf-h-auto" src="<?php echo $profile_image_src; ?>">
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
            <p class="userList_edit_permissions dcf-p-0 dcf-m-0 dcf-mb-3 dcf-txt-center">
                <a class="dcf-btn dcf-btn-secondary dcf-txt-xs" href="<?php echo $user->getEditPermissionsURL($context->calendar); ?>">Edit</a>
            </p>
        </li>
    <?php endforeach; ?>
</ul>
<div>
    <table class="dcf-table dcf-table-bordered dcf-w-100%">
        <thead>
            <tr>
                <th>User</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($context->getUsers() as $user): ?>
            <tr>
                <td>
                    <?php echo $user->uid; ?>
                </td>
                <td class="small-center table-actions">
                    <a class="dcf-btn dcf-btn-primary" href="<?php echo $user->getEditPermissionsURL($context->calendar) ?>">Edit Permissions</a>
                    <br class="dcf-d-none small-block" /><br class="dcf-d-none small-block" />
                    <form method="post" action="<?php echo $user->getDeletePermissionsURL($context->calendar) ?>" class="dcf-form delete-form">
                        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                        <input type="hidden" name="user_uid" value="<?php echo $user->uid ?>" />
                        <button class="dcf-btn dcf-btn-secondary" type="submit">Remove</button>
                    </form>
                </td>
            </tr>
        </tbody>
       <?php endforeach; ?>
    </table>
</div>
<br>
<a class="dcf-btn dcf-btn-primary" href="<?php echo $base_manager_url . $context->calendar->shortname ?>/users/new/">Add User</a>
