<?php



class LogType
{
	const __default = self::Info;

	const Info = 0;
	const Error = 1;
	const Debug = 2;
}

class TblLog extends Tbl
{
	public function mk($mType, $mX)
	{
		$this->insertValues($mType, date('Y-m-d G:i:s'), $mX);
	}
}

class TblTag extends Tbl
{
	public function mk($mX)
	{
		$this->insertValues($mX);
	}
}



class TblSubThread extends Tbl
{
	public function mk(...$mArgs)
	{
		return SPRCS::$mkSubscriptionThread->call(...$mArgs);
	}

	public function mkCU(...$mArgs)
	{
		return $this->mk(Creds::getCUID(), ...$mArgs);
	}

	public function has($mIDSubscriptor, $mIDThread)
	{
		$qres = $this->getWhere('id_thread = '.$mIDThread);
		if(!$qres) return false;

		while($row = $qres->fetch_assoc())
		{
			$baseRow = TBS::$subBase->findByID($row['id_base']);
			if(!$baseRow) continue;

			if($baseRow['id_subscriptor'] == $mIDSubscriptor) return true;
		}

		return false;
	}

	public function delCU($mIDThread)
	{
		$idSubscriptor = Creds::getCUID();

		$res = DB::query('SELECT ts.id
			FROM tbl_subscription_thread AS ts
			INNER JOIN tbl_subscription_base AS tb ON ts.id_base = tb.id 
			WHERE ts.id_thread = '.$mIDThread.' AND tb.id_subscriptor = '.$idSubscriptor.'');

		while($row = $res->fetch_assoc())
		{
			$this->deleteByID($row['id']);
		}		
/*
		$qres = $this->getWhere('id_thread = '.$mIDThread);
		if(!$qres) return false;

		while($row = $qres->fetch_assoc())
		{
			$baseRow = TBS::$subBase->findByID($row['id_base']);
			if(!$baseRow) continue;

			if($baseRow['id_subscriptor'] == $idSubscriptor) 
			{
				TBS::$subBase->deleteByID($baseRow['id']);
				return true;
			}
		}
	*/
	}
}

class TblSubUser extends Tbl
{
	public function mk(...$mArgs)
	{
		return SPRCS::$mkSubscriptionUser->call(...$mArgs);
	}
}

class TblSubTag extends Tbl
{
	public function mk(...$mArgs)
	{
		return SPRCS::$mkSubscriptionTag->call(...$mArgs);
	}
}



class TblNtfBase extends Tbl
{
	public function delAllCU()
	{
		$cuid = Creds::getCUID();
		return DB::query("DELETE FROM tbl_notification_base where id_receiver = $cuid");
	}

	public function markAllCU()
	{
		$cuid = Creds::getCUID();
		return DB::query("UPDATE tbl_notification_base SET seen = true where id_receiver = $cuid");
	}
}


class TblNtfThread extends Tbl
{
	public function getNtfs($mSeen)
	{
		$uid = Creds::getCUID();

		return DB::query(
			'SELECT tn.id, tn.id_subscription_thread, tb.seen, tnb.id_receiver, tn.id_post, ts.id_thread, tn.id_base, tb.creation_timestamp
			FROM  tbl_notification_thread as tn 
			INNER JOIN tbl_notification_base as tb ON tn.id_base = tb.id 
			INNER JOIN tbl_subscription_thread as ts ON tn.id_subscription_thread = ts.id  
			INNER JOIN tbl_notification_base as tnb ON tn.id_base = tnb.id
			WHERE tb.seen = '.$mSeen.' AND tnb.id_receiver = '.$uid.' ORDER BY tb.creation_timestamp DESC;');

	}

	public function getUnseen()
	{
		return $this->getNtfs('false');
	}
	public function getSeen()
	{
		return $this->getNtfs('true');
	}

	public function mk(...$mArgs)
	{
		return SPRCS::$mkNotificationThread->call(...$mArgs);
	}


}

class TblNtfUser extends Tbl
{
	public function mk(...$mArgs)
	{
		return SPRCS::$mkNotificationUser->call(...$mArgs);
	}
}

class TblNtfTag extends Tbl
{
	public function mk(...$mArgs)
	{
		return SPRCS::$mkNotificationTag->call(...$mArgs);
	}
}



TBS::$log = new TblLog('tbl_log',
	'type', 'creation_timestamp', 'value');

TBS::$tag = new TblTag('tbl_tag',
	'value');

TBS::$group = new TblGroup('tbl_group',
	'id_parent', 'name', 'is_superadmin', 'can_manage_sections', 'can_manage_users', 'can_manage_groups', 'can_manage_permissions');

TBS::$user = new TblUser('tbl_user',
	'id_group', 'username', 'password_hash', 'email', 'registration_date', 'firstname', 'lastname', 'birth_date');

TBS::$section = new TblSection('tbl_section',
	'id_parent', 'name');

TBS::$cntBase = new Tbl('tbl_content_base');
TBS::$cntThread = new TblCntThread('tbl_content_thread');
TBS::$cntPost = new TblCntPost('tbl_content_post');
TBS::$cntAttachment = new TblCntAttachment('tbl_content_attachment');

TBS::$subBase = new Tbl('tbl_subscription_base');
TBS::$subThread = new TblSubThread('tbl_subscription_thread');
TBS::$subUser = new TblSubUser('tbl_subscription_user');
TBS::$subTag = new TblSubTag('tbl_subscription_tag');

TBS::$ntfBase = new TblNtfBase('tbl_notification_base');
TBS::$ntfThread = new TblNtfThread('tbl_notification_thread');
TBS::$ntfUser = new TblNtfUser('tbl_notification_user');
TBS::$ntfTag = new TblNtfTag('tbl_notification_tag');


TBS::$gsperms = new TblGroupSectionPermission('tbl_group_section_permission',
	'id_group', 'id_section', 'can_view', 'can_post', 'can_create_thread', 'can_delete_post', 'can_delete_thread', 'can_delete_section');

?>