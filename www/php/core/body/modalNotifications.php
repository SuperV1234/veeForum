<?php

	$modalNtf = new BSModal('modalNtf', 'Notifications');
	$modalNtfBody = $modalNtf->inBody()->addAttribute('id', 'modalNtfBody');

	//refreshNotificationsModal($modalNtfBody);

	$modalNtf->inFooterBtns()
		->inBSLinkBtnActive('TODO', 'markAllNtfs();')
			->bsIcon('ok')
			->literal(' Mark all as seen')
			->out()
		->inBSLinkBtnActive('TODO', 'delAllNtfs();')
			->bsIcon('remove')
			->literal(' Delete all')
			->out()
	->printRoot();

	Gen::JS_PostAction('markAllNtfs()', 'markAllNtfs', [], 'refreshNotificationsModal(); reloadNavbar();', 'showModalInfo("Error", mErr);');
	Gen::JS_PostAction('delAllNtfs()', 'delAllNtfs', [], 'refreshNotificationsModal(); reloadNavbar();', 'showModalInfo("Error", mErr);');

	Gen::JS_PostAction('refreshNotificationsModal()', 'refreshNotificationsModal', [], '$("#modalNtfBody").html(mOut);', 'showModalInfo("Error", mErr);');
	Gen::JS_PostAction('delNtfByID(mX)', 'delNtfByID', ['id' => 'mX'], 'refreshNotificationsModal(); reloadNavbar();', 'showModalInfo("Error", mErr);');
	Gen::JS_PostAction('markNtfByID(mX)', 'markNtfByID', ['id' => 'mX'], 'refreshNotificationsModal(); reloadNavbar();', 'showModalInfo("Error", mErr);');
?>