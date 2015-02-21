<?php
	$notificationCount = TBS::$ntfBase->getWhere('id_receiver = '.Creds::getCUID().' AND seen = false')->num_rows;

	$modalNtf = new BSModal('modalNtf', 'Notifications');
	// $modalNtf->inHeader()->literal('ID: '.Session::get(SK::$threadID));
	$modalNtfBody = $modalNtf->inBody();

	for($i = 0; $i < $notificationCount; ++$i)
	{
		$modalNtfBody
			->inBSPanelNoHeader()
				->inBSBtnGroup('pull-left')
					->inBSLinkBtn('TODO', 'btn-xs')					
						->bsIcon('remove')
						->out()
					->inBSLinkBtn('TODO', 'btn-xs')					
						->bsIcon('arrow-right')
						->out()
					->out()
				->inDiv(['style' => 'float: left; padding-left: 10px;'])
					->strong('Notification blablabla');										
	}

	$modalNtf->inFooterBtns()
		->inBSLinkBtn('TODO')
			->bsLinkBtnAddDismissModal()
			->bsIcon('ok')
			->literal(' Mark all as seen')
			->out()
	->printRoot();




	$navLeft = (new Container())->inHTMLCtrl('ul', ['class' => 'nav navbar-nav']);
	
	

	$navLeft
		->inHTMLCtrl('li')
			->inDiv(['class' => 'navbar-form'])
				->inBSLinkBtnActive('btnShowNotifications', $modalNtf->jsShow())
					->strong(' '.$notificationCount.' ') 
					->bsIcon('list-alt');

	$navLeft
		->inHTMLCtrl('li')
			->inHTMLCtrl('a', ['id' => 'btnNavSections', 'href' => '#'])
				->literal('Sections');

	if(Creds::hasCUPrivilege(Privs::isSuperAdmin))
	{
		$navLeft
			->inHTMLCtrl('li')
				->inHTMLCtrl('a', ['id' => 'btnNavAdministration', 'href' => '#'])
					->literal('Administration');
	}

	$navRight = (new Container())->inHTMLCtrl('ul', ['class' => 'nav navbar-nav navbar-right']);
	$navRight
		->inHTMLCtrl('li')
			->inHTMLCtrl('a')
				->literal('Logged in as:')
				->inHTMLCtrl('strong')
					->literal(Creds::getCURow()['username']);					
	$navRight
		->inHTMLCtrl('li')
			->inDiv(['class' => 'navbar-form'])
				->inBSLinkBtnActive('btnSignOut', 'trySignOut()')
					->literal('Sign out');

	$navLeft->printRoot();
	$navRight->printRoot();

	Gen::JS_PostAction('trySignOut()', 'trySignOut',
				[],
				'reloadNavbar(); reloadPage();');

	Gen::JS_PostAction('changeCurrentPage(mX)', 'changeCurrentPage',
				[ 'idpage' => 'mX' ],
				'reloadNavbar(); reloadPage();');

	Gen::JS_PostAction('gotoThread(mX)', 'gotoThread',
				[ 'idThread' => 'mX' ],
				'changeCurrentPage('.PK::$threadView.');');

	Gen::JS_OnBtnClick('btnNavSections', 'changeCurrentPage('.PK::$sections.'); ');
	Gen::JS_OnBtnClick('btnNavAdministration', 'changeCurrentPage('.PK::$administration.'); ');
?>