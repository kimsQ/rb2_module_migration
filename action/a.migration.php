<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

include_once $g['path_core'].'function/rss.func.php';
function getMbrUid($id)
{
	global $table,$DB_CONNECT;
	$R = getDbData($table['s_mbrid'],'id="'.$id.'"','*');
	if($R['uid']) return $R['uid'];
	else return 0;
}
function getRssAddslashes($s,$f)
{
	return addslashes(getRssContent($s,$f));
}
$RSS = array();

$tmpname	= $_FILES['xmlfile']['tmp_name'];
$realname	= $_FILES['xmlfile']['name'];
$fileExt	= strtolower(getExt($realname));

if (is_uploaded_file($tmpname))
{
	if (!strpos('[xml]',$fileExt))
	{
		getLink('','','xml 파일만 등록할 수 있습니다.','');
	}
	$RSS['data'] = implode('',file($tmpname));
}
else
{
	if (!trim($xmlpath))
	{
		getLink('reload','parent.','XML파일경로가 지정되지 않았습니다.','');
	}
	$RSS['data'] = getUrlData(trim($xmlpath),1);
	if (!$RSS['data'])
	{
		getLink('reload','parent.','입력하신 XML파일경로를 접근할 수 없습니다.','');
	}
}

$mCount = 0;
$RSS['array']= explode('<item>',$RSS['data']);
$RSS['count']= count($RSS['array']);

//회원정보
if ($migtype == 'member') {
	if (getRssAddslashes($RSS['data'],'title') != 'KIMSQ_RB_MEMBER')
	{
		getLink('reload','parent.','킴스큐Rb용 마이그레이션 XML데이터 파일이 아닙니다.','');
	}
	$site		= $s;

	for($i = 1; $i < $RSS['count']; $i++)
	{
		$id			= getRssAddslashes($RSS['array'][$i],'id');
		$pw			= getRssAddslashes($RSS['array'][$i],'pw');

		$isMember	= getDbData($table['s_mbrid'],"id='".$id."'",'*');
		if ($isMember['uid']) continue;

		getDbInsert($table['s_mbrid'],'site,id,pw',"'$site','$id','$pw'");
		$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');
		$auth		= getRssAddslashes($RSS['array'][$i],'auth');;
		$mygroup		= getRssAddslashes($RSS['array'][$i],'sosok');;
		$level		= getRssAddslashes($RSS['array'][$i],'level');
		$comp		= 0;
		$admin		= getRssAddslashes($RSS['array'][$i],'admin');
		$adm_view	= getRssAddslashes($RSS['array'][$i],'adm_view');
		$email		= getRssAddslashes($RSS['array'][$i],'email');
		$name		= getRssAddslashes($RSS['array'][$i],'name');
		$nic		= getRssAddslashes($RSS['array'][$i],'nic');
		$grade		= '';
		$photo		= '';
		$home		= getRssAddslashes($RSS['array'][$i],'home');
		$sex		= getRssAddslashes($RSS['array'][$i],'sex');
		$birth1		= getRssAddslashes($RSS['array'][$i],'birth1');
		$birth2		= getRssAddslashes($RSS['array'][$i],'birth2');
		$birthtype	= 0;
		$tel		= getRssAddslashes($RSS['array'][$i],'tel1');
		$phone	= str_replace('-','',getRssAddslashes($RSS['array'][$i],'tel2'));
		$zip		= getRssAddslashes($RSS['array'][$i],'zip');
		$addr0		= getRssAddslashes($RSS['array'][$i],'addr0');
		$addr1		= getRssAddslashes($RSS['array'][$i],'addr1');
		$addr2		= getRssAddslashes($RSS['array'][$i],'addr2');
		$job		= getRssAddslashes($RSS['array'][$i],'job');
		$marr1		= getRssAddslashes($RSS['array'][$i],'marr1');
		$marr2		= getRssAddslashes($RSS['array'][$i],'marr2');
		$sms		= getRssAddslashes($RSS['array'][$i],'sms');
		$mailing	= getRssAddslashes($RSS['array'][$i],'mailing');
		$smail		= getRssAddslashes($RSS['array'][$i],'smail');
		$point		= getRssAddslashes($RSS['array'][$i],'point');
		$usepoint	= getRssAddslashes($RSS['array'][$i],'usepoint');
		$money		= getRssAddslashes($RSS['array'][$i],'money');
		$cash		= getRssAddslashes($RSS['array'][$i],'cash');
		$num_login	= getRssAddslashes($RSS['array'][$i],'num_login');
		$now_log	= 0;
		$last_log	= getRssAddslashes($RSS['array'][$i],'last_log');
		$last_pw	= getRssAddslashes($RSS['array'][$i],'last_pw');
		$is_paper	= 0;
		$d_regis	= getRssAddslashes($RSS['array'][$i],'d_regis');
		$addfield	= getRssAddslashes($RSS['array'][$i],'addfield');
		$sns		= getRssAddslashes($RSS['array'][$i],'sns');
		$addfield		= getRssAddslashes($RSS['array'][$i],'addfield');
		$tmpcode	= '';

		$_QKEY = "memberuid,site,auth,mygroup,level,comp,super,admin,adm_view,";
		$_QKEY.= "email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel,phone,";
		$_QKEY.= "job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,";
		$_QKEY.= "now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,addfield";
		$_QVAL = "'$memberuid','$site','$auth','$mygroup','$level','$comp','$admin','$admin','$adm_view',";
		$_QVAL.= "'$email','$name','$nic','$grade','$photo','$home','$sex','$birth1','$birth2','$birthtype','$tel','$phone',";
		$_QVAL.= "'$job','$marr1','$marr2','$sms','$mailing','$smail','$point','$usepoint','$money','$cash','$num_login',";
		$_QVAL.= "'$now_log','$last_log','$last_pw','$is_paper','$d_regis','$tmpcode','$sns','$addfield'";
		getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
		getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$level);
		getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$mygroup);

		$d_verified = '';
		$isEmail	= getDbData($table['s_mbremail'],"email='".$email."'",'uid');
		$isPhone	= getDbData($table['s_mbrphone'],"phone='".$phone."'",'uid');

		if ($email && !$isEmail){
			getDbInsert($table['s_mbremail'],'mbruid,email,base,backup,d_regis,d_code,d_verified',"'".$memberuid."','".$email."',1,0,'".$d_regis."','','".$d_verified."'");
		}

		if ($phone && !$isPhone) {
			getDbInsert($table['s_mbrphone'],'mbruid,phone,base,backup,d_regis,d_code,d_verified',"'".$memberuid."','".$phone."',1,0,'".$d_regis."','','".$d_verified."'");
		}

		if ($comp) {
			$comp_num	= getRssAddslashes($RSS['array'][$i],'comp_num');
			$comp_type	= getRssAddslashes($RSS['array'][$i],'comp_type');
			$comp_name	= getRssAddslashes($RSS['array'][$i],'comp_name');
			$comp_ceo	= getRssAddslashes($RSS['array'][$i],'comp_ceo');
			$comp_condition	= getRssAddslashes($RSS['array'][$i],'comp_condition');
			$comp_item = getRssAddslashes($RSS['array'][$i],'comp_item');
			$comp_tel	= getRssAddslashes($RSS['array'][$i],'comp_tel');
			$comp_fax	= getRssAddslashes($RSS['array'][$i],'comp_fax');
			$comp_zip	= getRssAddslashes($RSS['array'][$i],'comp_zip');
			$comp_addr0	= getRssAddslashes($RSS['array'][$i],'comp_addr0');
			$comp_addr1	= getRssAddslashes($RSS['array'][$i],'comp_addr1');
			$comp_addr2	= getRssAddslashes($RSS['array'][$i],'comp_addr2');
			$comp_part	= getRssAddslashes($RSS['array'][$i],'comp_part');
			$comp_level	= getRssAddslashes($RSS['array'][$i],'comp_level');

			$_QKEY = "memberuid,comp_num,comp_type,comp_name,comp_ceo,comp_condition,comp_item,";
			$_QKEY.= "comp_tel,comp_fax,comp_zip,comp_addr0,comp_addr1,comp_addr2,comp_part,comp_level";
			$_QVAL = "'$memberuid','$comp_num','$comp_type','$comp_name','$comp_ceo','$comp_condition','$comp_item',";
			$_QVAL.= "'$comp_tel','$comp_fax','$comp_zip','$comp_addr0','$comp_addr1','$comp_addr2','$comp_part','$comp_level'";
			getDbInsert($table['s_mbrcomp'],$_QKEY,$_QVAL);
		}

		$mCount++;

	}
}

//게시물,댓글
if ($migtype == 'board') {
	if (getRssAddslashes($RSS['data'],'title') != 'KIMSQ_RB_BBS')
	{
		getLink('reload','parent.','킴스큐Rb용 마이그레이션 XML데이터 파일이 아닙니다.','');
	}

	$str_today	= '';
	$str_month	= '';
	$bbsmodule	= 'bbs';
	$site		= $s;
	$mingid		= getDbCnt($table['bbsdata'],'min(gid)','');
	$gid		= $mingid ? $mingid-1 : 100000000.00;
	$bbsexp		= explode(',',$bbs);
	$bbsuid		= $bbsexp[0];
	$bbsid		= $bbsexp[1];


	for($i = 1; $i < $RSS['count']; $i++) {
		$depth		= getRssAddslashes($RSS['array'][$i],'depth');
		$parentmbr	= getRssAddslashes($RSS['array'][$i],'parentmbr');
		$display	= getRssAddslashes($RSS['array'][$i],'display');
		$hidden		= getRssAddslashes($RSS['array'][$i],'hidden');
		$notice		= getRssAddslashes($RSS['array'][$i],'notice');
		$name		= getRssAddslashes($RSS['array'][$i],'name');
		$nic		= getRssAddslashes($RSS['array'][$i],'nic');
		$id		= getRssAddslashes($RSS['array'][$i],'id');
		$mbruid		= getMbrUid($id);
		$pw			= getRssAddslashes($RSS['array'][$i],'pw');
		$category	= getRssAddslashes($RSS['array'][$i],'category');
		$subject	= getRssAddslashes($RSS['array'][$i],'subject');
		$content	= getRssAddslashes($RSS['array'][$i],'content');
		$html		= getRssAddslashes($RSS['array'][$i],'html');
		$tag		= getRssAddslashes($RSS['array'][$i],'tag');
		$hit		= getRssAddslashes($RSS['array'][$i],'hit');
		$down		= getRssAddslashes($RSS['array'][$i],'down');
		$comment	= getRssAddslashes($RSS['array'][$i],'comment');
		$commentdata= getRssAddslashes($RSS['array'][$i],'commentdata');
		$oneline	= getRssAddslashes($RSS['array'][$i],'oneline');
		$trackback	= getRssAddslashes($RSS['array'][$i],'trackback');
		$score1		= getRssAddslashes($RSS['array'][$i],'score1');
		$score2		= getRssAddslashes($RSS['array'][$i],'score2');
		$singo		= getRssAddslashes($RSS['array'][$i],'singo');
		$d_regis	= getRssAddslashes($RSS['array'][$i],'d_regis');
		$d_modify	= getRssAddslashes($RSS['array'][$i],'d_modify');
		$d_comment	= getRssAddslashes($RSS['array'][$i],'d_comment');
		$upload		= getRssAddslashes($RSS['array'][$i],'upload');
		$ip			= getRssAddslashes($RSS['array'][$i],'ip');
		$agent		= getRssAddslashes($RSS['array'][$i],'agent');
		$adddata	= getRssAddslashes($RSS['array'][$i],'adddata');

		$html = $html?$html:'HTML';
		$today		= substr($d_regis,0,8);
		$month		= substr($today,0,6);

		$xupload = '';
		$up_sync = '';

		if ($upload) {
			$up_fserver = 0;
			$up_url = '';
			$up_caption	= $subject;
			$upfiles = explode('|',$upload);

			foreach($upfiles as $val)
			{
				if (!$val) continue;
				$valexp		= explode(',',$val);
				$up_name	= $valexp[0];
				$up_tmpname	= $valexp[1];
				$up_size	= $valexp[2];
				$up_width	= $valexp[3];
				$up_height	= $valexp[4];
				$up_down	= $valexp[5];
				$up_date	= $valexp[6];
				$up_folder	= 'files/'.$valexp[7];
				$up_fileExt	= strtolower(getExt($up_name));
				$up_fileExt	= $up_fileExt == 'jpeg' ? 'jpg' : $up_fileExt;
				$up_type	= getFileType($up_fileExt);
				$up_mingid	= getDbCnt($table['s_upload'],'min(gid)','');
				$up_gid = $up_mingid ? $up_mingid - 1 : 100000000;

				$QKEY = "gid,hidden,tmpcode,site,mbruid,type,ext,fserver,host,folder,name,tmpname,size,width,height,caption,down,d_regis,d_update,sync";
				$QVAL = "'$up_gid','0','','$site','$mbruid','$up_type','$up_fileExt','$up_fserver','$up_url','$up_folder','$up_name','$up_tmpname','$up_size','$up_width','$up_height','$up_caption','$up_down','$d_regis','','$up_sync'";
				getDbInsert($table['s_upload'],$QKEY,$QVAL);
				$up_lastuid = getDbCnt($table['s_upload'],'max(uid)','');
				$xupload .= '['.$up_lastuid.']';

				if ($up_gid == 100000000) db_query("OPTIMIZE TABLE ".$table['s_upload'],$DB_CONNECT);
			}
		}

		$QKEY = "site,gid,bbs,bbsid,depth,parentmbr,display,hidden,notice,name,nic,mbruid,id,pw,category,subject,content,html,tag,";
		$QKEY.= "hit,down,comment,oneline,likes,dislikes,report,point1,point2,point3,point4,d_regis,d_modify,d_comment,upload,ip,agent,sns,adddata";
		$QVAL = "'$site','$gid','$bbsuid','$bbsid','$depth','$parentmbr','$display','$hidden','$notice','$name','$nic','$mbruid','$id','$pw','$category','$subject','$content','$html','$tag',";
		$QVAL.= "'$hit','$down','$comment','$oneline','$score1','$score2','$singo','0','0','0','0','$d_regis','$d_modify','$d_comment','$xupload','$ip','$agent','','$adddata'";
		getDbInsert($table[$bbsmodule.'data'],$QKEY,$QVAL);
		$LASTUID = getDbCnt($table[$bbsmodule.'data'],'max(uid)','');

		getDbInsert($table[$bbsmodule.'idx'],'site,notice,bbs,gid',"'$site','$notice','$bbsuid','$gid'");
		getDbUpdate($table[$bbsmodule.'list'],"num_r=num_r+1,d_last='".$d_regis."'",'uid='.$bbsuid);

		if(!strstr($str_month,'['.$month.']') && !getDbRows($table[$bbsmodule.'month'],"date='".$month."' and site=".$site.' and bbs='.$bbsuid))
		{
			getDbInsert($table[$bbsmodule.'month'],'date,site,bbs,num',"'".$month."','".$site."','".$bbsuid."','1'");
			$str_month .= '['.$month.']';
		}
		else {
			getDbUpdate($table[$bbsmodule.'month'],'num=num+1',"date='".$month."' and site=".$site.' and bbs='.$bbsuid);
		}

		if(!strstr($str_today,'['.$today.']') && !getDbRows($table[$bbsmodule.'day'],"date='".$today."' and site=".$site.' and bbs='.$bbsuid))
		{
			getDbInsert($table[$bbsmodule.'day'],'date,site,bbs,num',"'".$today."','".$site."','".$bbsuid."','1'");
			$str_today .= '['.$today.']';
		}
		else {
			getDbUpdate($table[$bbsmodule.'day'],'num=num+1',"date='".$today."' and site=".$site.' and bbs='.$bbsuid);
		}

		if ($gid == 100000000.00)
		{
			db_query("OPTIMIZE TABLE ".$table[$bbsmodule.'idx'],$DB_CONNECT);
			db_query("OPTIMIZE TABLE ".$table[$bbsmodule.'data'],$DB_CONNECT);
			db_query("OPTIMIZE TABLE ".$table[$bbsmodule.'month'],$DB_CONNECT);
			db_query("OPTIMIZE TABLE ".$table[$bbsmodule.'day'],$DB_CONNECT);
		}

		if ($comment) {
			$comment_array = explode('<commentarea>',$commentdata);
			$comment_count = count($comment_array);

			$c_parent	= $bbsmodule.$LASTUID;
			$c_parentmbr= $mbruid;
			$c_minuid	= getDbCnt($table['s_comment'],'min(uid)','');
			$c_uid		= $c_minuid ? $c_minuid-1 : 1000000000;

			for ($j = 0; $j < $comment_count; $j++)
			{
				if (!$comment_array[$j]) continue;

				$c_display	= getRssAddslashes($comment_array[$j],'c_display');
				$c_hidden	= getRssAddslashes($comment_array[$j],'c_hidden');
				$c_notice	= getRssAddslashes($comment_array[$j],'c_notice');
				$c_name		= getRssAddslashes($comment_array[$j],'c_name');
				$c_nic		= getRssAddslashes($comment_array[$j],'c_nic');
				$c_id	= getRssAddslashes($comment_array[$j],'c_id');
				$c_mbruid	= getMbrUid($c_id);
				$c_pw		= getRssAddslashes($comment_array[$j],'c_pw');
				$c_subject	= getRssAddslashes($comment_array[$j],'c_subject');
				$c_content	= getRssAddslashes($comment_array[$j],'c_content');
				$c_html		= getRssAddslashes($comment_array[$j],'c_html');
				$c_hit		= getRssAddslashes($comment_array[$j],'c_hit');
				$c_down		= getRssAddslashes($comment_array[$j],'c_down');
				$c_oneline	= getRssAddslashes($comment_array[$j],'c_oneline');
				$c_score1	= getRssAddslashes($comment_array[$j],'c_score1');
				$c_score2	= getRssAddslashes($comment_array[$j],'c_score2');
				$c_singo	= getRssAddslashes($comment_array[$j],'c_singo');
				$c_d_regis	= getRssAddslashes($comment_array[$j],'c_d_regis');
				$c_d_modify	= getRssAddslashes($comment_array[$j],'c_d_modify');
				$c_d_oneline= getRssAddslashes($comment_array[$j],'c_d_oneline');
				$c_upload	= getRssAddslashes($comment_array[$j],'c_upload');
				$c_ip		= getRssAddslashes($comment_array[$j],'c_ip');
				$c_agent	= getRssAddslashes($comment_array[$j],'c_agent');
				$c_sync		= '['.$bbsmodule.']['.$LASTUID.'][uid,comment,oneline,d_comment]['.$table[$bbsmodule.'data'].']['.$c_parentmbr.'][m:'.$bbsmodule.',bid:'.$bbsid.',uid:'.$LASTUID.']';
				$c_adddata	= getRssAddslashes($comment_array[$j],'c_adddata');
				$onelinedata= getRssAddslashes($comment_array[$j],'onelinedata');

				$xupload = '';
				$up_sync = '';
				if ($c_upload)
				{
					$up_fserver = 0;
					$up_url = $g['url_root'].'/files/';
					$up_caption	= $c_subject;
					$upfiles = explode('|',$c_upload);

					foreach($upfiles as $val)
					{
						if (!$val) continue;
						$valexp		= explode(',',$val);
						$up_name	= $valexp[0];
						$up_tmpname	= $valexp[1];
						$up_size	= $valexp[3];
						$up_width	= $valexp[7];
						$up_height	= $valexp[8];
						$up_down	= $valexp[4];
						$up_date	= $valexp[5];
						$up_folder	= $valexp[6];
						$up_fileExt	= strtolower(getExt($up_name));
						$up_fileExt	= $up_fileExt == 'jpeg' ? 'jpg' : $up_fileExt;
						$up_type	= getFileType($up_fileExt);
						$up_mingid	= getDbCnt($table['s_upload'],'min(gid)','');
						$up_gid = $up_mingid ? $up_mingid - 1 : 100000000;

						$QKEY = "gid,hidden,tmpcode,site,mbruid,type,ext,fserver,host,folder,name,tmpname,size,width,height,caption,down,d_regis,d_update,sync";
						$QVAL = "'$up_gid','0','','$site','$c_mbruid','$up_type','$up_fileExt','$up_fserver','$up_url','$up_folder','$up_name','$up_tmpname','$up_size','$up_width','$up_height','$up_caption','$up_down','$d_regis','','$up_sync'";
						getDbInsert($table['s_upload'],$QKEY,$QVAL);
						$up_lastuid = getDbCnt($table['s_upload'],'max(uid)','');
						$xupload .= '['.$up_lastuid.']';

						if ($up_gid == 100000000) db_query("OPTIMIZE TABLE ".$table['s_upload'],$DB_CONNECT);
					}
				}

				$QKEY = "uid,site,parent,parentmbr,display,hidden,notice,name,nic,mbruid,id,pw,subject,content,html,";
				$QKEY.= "hit,down,oneline,likes,dislikes,report,point,d_regis,d_modify,d_oneline,upload,ip,agent,sync,sns,adddata";
				$QVAL = "'$c_uid','$site','$c_parent','$c_parentmbr','$c_display','$c_hidden','$c_notice','$c_name','$c_nic','$c_mbruid','$c_id','$c_pw','$c_subject','$c_content','$c_html',";
				$QVAL.= "'$c_hit','$c_down','$c_oneline','$c_score1','$c_score2','$c_singo','0','$c_d_regis','$c_d_modify','$c_d_oneline','$xupload','$c_ip','$c_agent','$c_sync','','$c_adddata'";
				getDbInsert($table['s_comment'],$QKEY,$QVAL);

				if ($c_uid == 1000000000) db_query("OPTIMIZE TABLE ".$table['s_comment'],$DB_CONNECT);

				if ($c_oneline)
				{

					$oneline_array = explode('<onelinearea>',$onelinedata);
					$oneline_count = count($oneline_array);
					$o_maxuid	= getDbCnt($table['s_oneline'],'max(uid)','');
					$o_uid		= $o_maxuid ? $o_maxuid+1 : 1;
					$o_parent	= $c_uid;
					$o_parentmbr= $c_mbruid;
					$o_hidden	= $c_hidden;

					for ($k = 0; $k < $oneline_count; $k++)
					{
						if (!$oneline_array[$k]) continue;

						$o_name		= getRssAddslashes($oneline_array[$k],'o_name');
						$o_nic		= getRssAddslashes($oneline_array[$k],'o_nic');
						$o_id	= getRssAddslashes($oneline_array[$k],'o_id');
						$o_mbruid	= getMbrUid($o_id);
						$o_content	= getRssAddslashes($oneline_array[$k],'o_content');
						$o_html		= getRssAddslashes($oneline_array[$k],'o_html');
						$o_singo	= getRssAddslashes($oneline_array[$k],'o_singo');
						$o_d_regis	= getRssAddslashes($oneline_array[$k],'o_d_regis');
						$o_d_modify	= getRssAddslashes($oneline_array[$k],'o_d_modify');
						$o_ip		= getRssAddslashes($oneline_array[$k],'o_ip');
						$o_agent	= getRssAddslashes($oneline_array[$k],'o_agent');
						$o_adddata	= '';

						$QKEY = "uid,site,parent,parentmbr,hidden,name,nic,mbruid,id,content,html,report,point,d_regis,d_modify,ip,agent,adddata";
						$QVAL = "'$o_uid','$site','$o_parent','$o_parentmbr','$o_hidden','$o_name','$o_nic','$o_mbruid','$o_id','$o_content','$o_html','$o_singo','0','$o_d_regis','$o_d_modify','$o_ip','$o_agent','$o_adddata'";
						getDbInsert($table['s_oneline'],$QKEY,$QVAL);

						if ($o_uid == 1) db_query("OPTIMIZE TABLE ".$table['s_oneline'],$DB_CONNECT);
						$o_uid++;
					}
				}
				$c_uid--;
			}
		}

		$mCount++;
		$gid--;
	}
}

//포스트
if ($migtype == 'post') {
	if (getRssAddslashes($RSS['data'],'title') != 'KIMSQ_RB_BBS') {
		getLink('reload','parent.','킴스큐Rb용 마이그레이션 XML데이터 파일이 아닙니다.','');
	}

	$str_today	= '';
	$str_month	= '';
	$postmodule	= 'post';
	$site		= $s;
	$cid = substr($g['time_srnad'],9,7);

	for($i = 1; $i < $RSS['count']; $i++) {
		$mingid		= getDbCnt($table[$postmodule.'data'],'min(gid)','');
		$gid		= $mingid ? $mingid-1 : 100000000.00;
		$hidden	= getRssAddslashes($RSS['array'][$i],'hidden');
		$display = $hidden==1?1:5;  //공개
		$id		= getRssAddslashes($RSS['array'][$i],'id');
	  $mbruid		= getMbrUid($id);
		$cid = $cid + $i;
	  $subject	= preg_replace("/\r\n|\r|\n/", "", getRssAddslashes($RSS['array'][$i],'subject'));
	  $content	= preg_replace("/\r\n|\r|\n/", "", getRssAddslashes($RSS['array'][$i],'content'));
		$tag		= getRssAddslashes($RSS['array'][$i],'tag');
	  $hit		= getRssAddslashes($RSS['array'][$i],'hit');
		$likes		= getRssAddslashes($RSS['array'][$i],'score1');
		$dislikes		= getRssAddslashes($RSS['array'][$i],'score2');
	  $comment = getRssAddslashes($RSS['array'][$i],'comment');
		$commentdata= getRssAddslashes($RSS['array'][$i],'commentdata');
	  $oneline = getRssAddslashes($RSS['array'][$i],'oneline');
	  $d_regis		= getRssAddslashes($RSS['array'][$i],'d_regis');
	  $d_modify		= getRssAddslashes($RSS['array'][$i],'d_modify');
		$upload		= getRssAddslashes($RSS['array'][$i],'upload');
		$ip		= getRssAddslashes($RSS['array'][$i],'ip');
		$agent		= getRssAddslashes($RSS['array'][$i],'agent');
		$adddata		= getRssAddslashes($RSS['array'][$i],'adddata');
		$html = 'HTML';
		if ($postcat) $category = '['.$postcat.']';
		$members = '['.$mbruid.']';
	  $format = 1;
	  $dis_rating = 1;

		$today		= substr($d_regis,0,8);
		$month		= substr($today,0,6);

		$xupload = '';
		$up_sync = '';

		if ($upload) {
			$up_fserver = 0;
			$up_url = '';
			$up_caption	= $subject;
			$upfiles = explode('|',$upload);

			foreach($upfiles as $val) {
				if (!$val) continue;
				$valexp		= explode(',',$val);
				$up_name	= $valexp[0];
				$up_tmpname	= $valexp[1];
				$up_size	= $valexp[2];
				$up_width	= $valexp[3];
				$up_height	= $valexp[4];
				$up_down	= $valexp[5];
				$up_date	= $valexp[6];
				$up_folder	= 'files/'.$valexp[7];
				$up_src	= '/'.$up_folder.'/'.$up_tmpname;
				$up_fileExt	= strtolower(getExt($up_name));
				$up_fileExt	= $up_fileExt == 'jpeg' ? 'jpg' : $up_fileExt;
				$up_type	= getFileType($up_fileExt);
				$up_mingid	= getDbCnt($table['s_upload'],'min(gid)','');
				$up_gid = $up_mingid ? $up_mingid - 1 : 100000000;

				$QKEY = "gid,pid,hidden,tmpcode,site,mbruid,type,ext,fserver,host,folder,name,tmpname,size,width,height,caption,src,down,d_regis,d_update";
				$QVAL = "'$up_gid','$up_gid','0','','$site','$mbruid','$up_type','$up_fileExt','$up_fserver','$up_url','$up_folder','$up_name','$up_tmpname','$up_size','$up_width','$up_height','$up_caption','$up_src','$up_down','$up_date',''";
				getDbInsert($table['s_upload'],$QKEY,$QVAL);
				$up_lastuid = getDbCnt($table['s_upload'],'max(uid)','');
				$xupload .= '['.$up_lastuid.']';

				if ($up_gid == 100000000) db_query("OPTIMIZE TABLE ".$table['s_upload'],$DB_CONNECT);
			}
		}

		// member -> members 필드명 변경
		$_tmp1 = db_query("SHOW COLUMNS FROM ".$table[$postmodule.'data']." WHERE `Field` = 'members'",$DB_CONNECT);
		if(!db_num_rows($_tmp1)) {
			$_tmp1 = ("alter table ".$table[$postmodule.'data']." CHANGE member members TEXT not null");
			db_query($_tmp1, $DB_CONNECT);
		}

	  $_QKEY = "site,gid,cid,mbruid,subject,review,content,tag,display,hidden,html,category,members,hit,likes,dislikes,comment,oneline,dis_rating,d_regis,d_modify,format,upload,ip,agent,adddata";
	  $_QVAL = "'$site','$gid','$cid','$mbruid','$subject','$review','$content','$tag','$display','$hidden','$html','$category','$members','$hit','$likes','$dislikes','$comment','$oneline','$dis_rating','$d_regis','$d_modify','$format','$xupload','$ip','$agent','$adddata'";
	  getDbInsert($table[$postmodule.'data'],$_QKEY,$_QVAL);
		getDbInsert($table[$postmodule.'index'],'site,display,format,gid',"'$site','$display','$format','$gid'");

	  $LASTUID = getDbCnt($table[$postmodule.'data'],'max(uid)','');
	  $QKEY2 = "mbruid,site,gid,data,display,format,auth,level,d_regis";
	  $QVAL2 = "'$mbruid','$site','$gid','$LASTUID','$display','$format','1','1','$d_regis'";
	  getDbInsert($table[$postmodule.'member'],$QKEY2,$QVAL2);

		if(!getDbRows($table['s_mbrmonth'],"date='".$date['month']."' and site=".$site.' and mbruid='.$mbruid)) {
	    getDbInsert($table['s_mbrmonth'],'date,site,mbruid,post_num',"'".$month."','".$site."','".$mbruid."','0'");
	  }

	  if(!getDbRows($table['s_mbrday'],"date='".$date['today']."' and site=".$site.' and mbruid='.$mbruid)) {
	    getDbInsert($table['s_mbrday'],'date,site,mbruid,post_num',"'".$today."','".$site."','".$mbruid."','0'");
	  }

	  getDbUpdate($table['s_mbrdata'],'num_post=num_post+1','memberuid='.$mbruid); // 회원포스트 수량 +1
	  getDbUpdate($table['s_mbrmonth'],'post_num=post_num+1',"date='".$month."' and site=".$site.' and mbruid='.$mbruid); //회원별 월별 수량등록
	  getDbUpdate($table['s_mbrday'],'post_num=post_num+1',"date='".$today."' and site=".$site.' and mbruid='.$mbruid); //회원별 일별 수량등록

		$postcat_info=getUidData($table[$postmodule.'category'],$postcat);
		$postcat_depth=$postcat_info['depth'];
		if (!getDbRows($table[$postmodule.'category_index'],'data='.$LASTUID.' and category='.$postcat))
		{
			getDbInsert($table[$postmodule.'category_index'],'site,data,category,depth,gid',"'".$site."','".$LASTUID."','".$postcat."','".$postcat_depth."','".$gid."'");
			getDbUpdate($table[$postmodule.'category'],'num=num+1','uid='.$postcat);
		}

		if ($gid == 100000000.00)
		{
			db_query("OPTIMIZE TABLE ".$table[$postmodule.'data'],$DB_CONNECT);
			db_query("OPTIMIZE TABLE ".$table[$postmodule.'month'],$DB_CONNECT);
			db_query("OPTIMIZE TABLE ".$table[$postmodule.'day'],$DB_CONNECT);
		}

		if ($comment) {

			$comment_array = explode('<commentarea>',$commentdata);
			$comment_count = count($comment_array);

			$c_parent	= $postmodule.$LASTUID;
			$c_parentmbr= $mbruid;
			$c_minuid	= getDbCnt($table['s_comment'],'min(uid)','');
			$c_uid		= $c_minuid ? $c_minuid-1 : 1000000000;

			for ($j = 0; $j < $comment_count; $j++) {
				if (!$comment_array[$j]) continue;

				$c_display	= getRssAddslashes($comment_array[$j],'c_display');
				$c_hidden	= getRssAddslashes($comment_array[$j],'c_hidden');
				$c_notice	= getRssAddslashes($comment_array[$j],'c_notice');
				$c_name		= getRssAddslashes($comment_array[$j],'c_name');
				$c_nic		= getRssAddslashes($comment_array[$j],'c_nic');
				$c_id	= getRssAddslashes($comment_array[$j],'c_id');
				$c_mbruid	= getMbrUid($c_id);
				$c_pw		= getRssAddslashes($comment_array[$j],'c_pw');
				$c_subject	= getRssAddslashes($comment_array[$j],'c_subject');
				$c_content	= getRssAddslashes($comment_array[$j],'c_content');
				$c_html		= getRssAddslashes($comment_array[$j],'c_html');
				$c_hit		= getRssAddslashes($comment_array[$j],'c_hit');
				$c_down		= getRssAddslashes($comment_array[$j],'c_down');
				$c_oneline	= getRssAddslashes($comment_array[$j],'c_oneline');
				$c_score1	= getRssAddslashes($comment_array[$j],'c_score1');
				$c_score2	= getRssAddslashes($comment_array[$j],'c_score2');
				$c_singo	= getRssAddslashes($comment_array[$j],'c_singo');
				$c_d_regis	= getRssAddslashes($comment_array[$j],'c_d_regis');
				$c_d_modify	= getRssAddslashes($comment_array[$j],'c_d_modify');
				$c_d_oneline= getRssAddslashes($comment_array[$j],'c_d_oneline');
				$c_upload	= getRssAddslashes($comment_array[$j],'c_upload');
				$c_ip		= getRssAddslashes($comment_array[$j],'c_ip');
				$c_agent	= getRssAddslashes($comment_array[$j],'c_agent');
				$c_sync		= $table[$postmodule.'data'].'|'.$postmodule.'|'.$LASTUID;
				$c_adddata	= getRssAddslashes($comment_array[$j],'c_adddata');
				$onelinedata= getRssAddslashes($comment_array[$j],'onelinedata');

				$QKEY = "uid,site,parent,parentmbr,display,hidden,notice,name,nic,mbruid,id,pw,subject,content,html,";
				$QKEY.= "hit,down,oneline,likes,dislikes,report,point,d_regis,d_modify,d_oneline,upload,ip,agent,sync,sns,adddata";
				$QVAL = "'$c_uid','$site','$c_parent','$c_parentmbr','$c_display','$c_hidden','$c_notice','$c_name','$c_nic','$c_mbruid','$c_id','$c_pw','$c_subject','$c_content','$c_html',";
				$QVAL.= "'$c_hit','$c_down','$c_oneline','$c_score1','$c_score2','$c_singo','0','$c_d_regis','$c_d_modify','$c_d_oneline','','$c_ip','$c_agent','$c_sync','','$c_adddata'";

				getDbInsert($table['s_comment'],$QKEY,$QVAL);

				if ($c_uid == 1000000000) db_query("OPTIMIZE TABLE ".$table['s_comment'],$DB_CONNECT);

				if ($c_oneline) {

					$oneline_array = explode('<onelinearea>',$onelinedata);
					$oneline_count = count($oneline_array);
					$o_maxuid	= getDbCnt($table['s_oneline'],'max(uid)','');
					$o_uid		= $o_maxuid ? $o_maxuid+1 : 1;
					$o_parent	= $c_uid;
					$o_parentmbr= $c_mbruid;
					$o_hidden	= $c_hidden;

					for ($k = 0; $k < $oneline_count; $k++) {
						if (!$oneline_array[$k]) continue;

						$o_name		= getRssAddslashes($oneline_array[$k],'o_name');
						$o_nic		= getRssAddslashes($oneline_array[$k],'o_nic');
						$o_id	= getRssAddslashes($oneline_array[$k],'o_id');
						$o_mbruid	= getMbrUid($o_id);
						$o_content	= getRssAddslashes($oneline_array[$k],'o_content');
						$o_html		= getRssAddslashes($oneline_array[$k],'o_html');
						$o_singo	= getRssAddslashes($oneline_array[$k],'o_singo');
						$o_d_regis	= getRssAddslashes($oneline_array[$k],'o_d_regis');
						$o_d_modify	= getRssAddslashes($oneline_array[$k],'o_d_modify');
						$o_ip		= getRssAddslashes($oneline_array[$k],'o_ip');
						$o_agent	= getRssAddslashes($oneline_array[$k],'o_agent');
						$o_adddata	= '';

						$QKEY = "uid,site,parent,parentmbr,hidden,name,nic,mbruid,id,content,html,report,point,d_regis,d_modify,ip,agent,adddata";
						$QVAL = "'$o_uid','$site','$o_parent','$o_parentmbr','$o_hidden','$o_name','$o_nic','$o_mbruid','$o_id','$o_content','$o_html','$o_singo','0','$o_d_regis','$o_d_modify','$o_ip','$o_agent','$o_adddata'";
						getDbInsert($table['s_oneline'],$QKEY,$QVAL);

						if ($o_uid == 1) db_query("OPTIMIZE TABLE ".$table['s_oneline'],$DB_CONNECT);
						$o_uid++;
					}
				}
				$c_uid--;
			}
		}

	  $mCount++;
		$gid--;
	}
}

//포인트
if ($migtype == 'point')
{
	if (getRssAddslashes($RSS['data'],'title') != 'KIMSQ_RB_POINT')
	{
		getLink('reload','parent.','킴스큐Rb용 마이그레이션 XML데이터 파일이 아닙니다.','');
	}
	for($i = 1; $i < $RSS['count']; $i++)
	{
		$my_mbruid = getMbrUid(getRssAddslashes($RSS['array'][$i],'my_mbrid'));
		$by_mbruid = getMbrUid(getRssAddslashes($RSS['array'][$i],'by_mbrid'));
		$price	   = getRssAddslashes($RSS['array'][$i],'price');
		$content   = getRssAddslashes($RSS['array'][$i],'content');
		$d_regis   = getRssAddslashes($RSS['array'][$i],'d_regis');

		$QKEY = 'my_mbruid,by_mbruid,price,content,d_regis';
		$QVAL = "'$my_mbruid','$by_mbruid','$price','$content','$d_regis'";
		getDbInsert($table['s_point'],$QKEY,$QVAL);

		$mCount++;
	}
}

//적립금
if ($migtype == 'cash')
{
	if (getRssAddslashes($RSS['data'],'title') != 'KIMSQ_RB_CASH')
	{
		getLink('reload','parent.','킴스큐Rb용 마이그레이션 XML데이터 파일이 아닙니다.','');
	}
	for($i = 1; $i < $RSS['count']; $i++)
	{
		$my_mbruid = getMbrUid(getRssAddslashes($RSS['array'][$i],'my_mbrid'));
		$by_mbruid = getMbrUid(getRssAddslashes($RSS['array'][$i],'by_mbrid'));
		$price	   = getRssAddslashes($RSS['array'][$i],'price');
		$content   = getRssAddslashes($RSS['array'][$i],'content');
		$d_regis   = getRssAddslashes($RSS['array'][$i],'d_regis');

		$QKEY = 'my_mbruid,by_mbruid,price,content,d_regis';
		$QVAL = "'$my_mbruid','$by_mbruid','$price','$content','$d_regis'";
		getDbInsert($table['s_cash'],$QKEY,$QVAL);

		$mCount++;
	}
}

//예치금
if ($migtype == 'money')
{
	if (getRssAddslashes($RSS['data'],'title') != 'KIMSQ_RB_MONEY')
	{
		getLink('reload','parent.','킴스큐Rb용 마이그레이션 XML데이터 파일이 아닙니다.','');
	}
	for($i = 1; $i < $RSS['count']; $i++)
	{
		$my_mbruid = getMbrUid(getRssAddslashes($RSS['array'][$i],'my_mbrid'));
		$by_mbruid = getMbrUid(getRssAddslashes($RSS['array'][$i],'by_mbrid'));
		$price	   = getRssAddslashes($RSS['array'][$i],'price');
		$content   = getRssAddslashes($RSS['array'][$i],'content');
		$d_regis   = getRssAddslashes($RSS['array'][$i],'d_regis');

		$QKEY = 'my_mbruid,by_mbruid,price,content,d_regis';
		$QVAL = "'$my_mbruid','$by_mbruid','$price','$content','$d_regis'";
		getDbInsert($table['s_money'],$QKEY,$QVAL);

		$mCount++;
	}
}

//쪽지
if ($migtype == 'msg')
{
	if (getRssAddslashes($RSS['data'],'title') != 'KIMSQ_RB_MESSAGE')
	{
		getLink('reload','parent.','킴스큐Rb용 마이그레이션 XML데이터 파일이 아닙니다.','');
	}
	for($i = 1; $i < $RSS['count']; $i++)
	{
		$parent	   = 0;
		$my_mbruid = getMbrUid(getRssAddslashes($RSS['array'][$i],'my_mbrid'));
		$by_mbruid = getMbrUid(getRssAddslashes($RSS['array'][$i],'by_mbrid'));
		$inbox	   = 1;
		$content   = getRssAddslashes($RSS['array'][$i],'content');
		$html	   = getRssAddslashes($RSS['array'][$i],'html');
		$upload	   = '';
		$d_regis   = getRssAddslashes($RSS['array'][$i],'d_regis');
		$d_read	   = getRssAddslashes($RSS['array'][$i],'d_read');

		$QKEY = 'parent,my_mbruid,by_mbruid,inbox,content,html,upload,d_regis,d_read';
		$QVAL = "'$parent','$my_mbruid','$by_mbruid','$inbox','$content','$html','$upload','$d_regis','$d_read'";
		getDbInsert($table['s_paper'],$QKEY,$QVAL);

		$mCount++;
	}
}

if ($viewresult) {
	if ($migtype == 'member')
	{
		echo '<meta http-equiv="content-type" content="text/html;charset=utf-8" />';
		echo '<script type="text/javascript">';
		echo "alert('[".number_format($mCount)."]건의 회원데이터 이전작업이 완료되었습니다.');";
		echo "parent.window.open('".$g['s'].'/?r='.$r.'&m=admin&module=member'."');";
		echo "parent.location.href='".$g['s'].'/?r='.$r.'&m=admin&module='.$m."';";
		echo '</script>';
	}
	if ($migtype == 'board')
	{
		echo '<meta http-equiv="content-type" content="text/html;charset=utf-8" />';
		echo '<script type="text/javascript">';
		echo "alert('[".number_format($mCount)."]건의 게시물데이터 이전작업이 완료되었습니다.');";
		echo "parent.window.open('".$g['s'].'/?r='.$r.'&m=bbs&bid='.$bbsid."');";
		echo "parent.location.href='".$g['s'].'/?r='.$r.'&m=admin&module='.$m."';";
		echo '</script>';
	}
}
else {
	getLink($g['s'].'/?r='.$r.'&m=admin&module='.$m,'parent.','['.number_format($mCount).']건의 데이터 이전작업이 완료되었습니다.','');
}
?>
