<?php 
set_time_limit(0);
$isdynamic=true;           ///////////���ļ���ʵ�ֶ�̬��ѯ�ģ���̬��ѯ����index_jt.php/////////

require '../include/common.inc.php';
$head['title']="���ڹ���·��,���ڹ���վ��,���ڹ���վվ��ѯ";
if(!$page) $page=1;
$pagesize=10;
$offset=($page-1)*$pagesize;

if($st!='')
{
	//վ���ѯ
  $st=trim($st);
	if(!strpos($st,'����վ��'))
	$head['title']=$st.'����վ��-'.$head['title'];
	
	$isstation=true;	//������ʾ��־Ϊtrue	
	
	$lines=array();
	
	$r1=$db->get_one("select a.stationid from myself_station a, myself_busline b where a.isdelete='0' and 
	(a.stationname='$st' or a.aliases1='$st' or a.aliases2='$st') and a.stationid=b.stationid and b.isdelete='0'");
	$stationid=$r1['stationid'];     //�ñ���Ҳ���ҵ�վ��
   
	if($stationid)   //�ҵ���վ��
	{
		unset($r1);	
		$isfind=true;
		
		$r=$db->get_one("SELECT count(*) as num FROM myself_busline WHERE stationid = '$stationid' AND isdelete =0");
		$total=$r['num']; //��¼����
		
		$query=$db->query("SELECT b.* FROM myself_busline a,myself_bus b WHERE a.stationid = '$stationid' AND a.isdelete =0 AND a.busid=b.id
		 ORDER BY id limit $offset,$pagesize");
		while($record=$db->fetch_array($query))   
		{			
			$record['updatetime']=date('Y-m-d',$record['updatetime']);
       		$record['attribute']=trim($record['attribute']);
       			
       		if(strpos($record['attribute'],' '))
       		{
       			$record['att1']=substr($record['attribute'],0,strpos($record['attribute'],' '));
       			$record['attribute']=trim(substr($record['attribute'],strpos($record['attribute'],' ')+1));
       		}
       		
       		if(strpos($record['attribute'],' '))
       		{
        		$record['att2']=substr($record['attribute'],0,strpos($record['attribute'],' '));
        		$record['attribute']=trim(substr($record['attribute'],strpos($record['attribute'],' ')+1));
        		
        		if(strpos($record['attribute'],' '))
        		{
        			$record['attribute']=str_replace(' ','<br>',$record['attribute']);
        		}
        	}
        	else
        	{
        		$record['att2']=$record['attribute'];
        		$record['attribute']='';
        	}	
			$lines[]=$record;	
		}
		$url='?st='.urlencode($st);
		$pagehtml=pageshtml($total,$page,$pagesize,$url);
		
		//��ȡվ���ͼ�����վ����Ϣ
		$r=$db->get_one("select * from myself_station where stationname='$st' and isdelete='0'");
		//��ȡվ��������ص�վ����Ϣ
		$query=$db->query("SELECT stationid,stationname FROM myself_station WHERE stationname like '%$st%' And stationname<>'$st' And isdelete=0 order by stationname");
		$stations=array();
		$count=$db->num_rows($query);
		if($count)
		{			
			$ismore=true;
			$cols=5;
			while ($record=$db->fetch_array($query))
			{				
				$stations[]=$record;
			}
			//������
			$record=array('','');		
			while ($count%$cols!=0)
			{
				$stations[]=$record;
				$count++;
			}
		}
		
	}
	else 
	{
		$isfind=false;
		$query=$db->query("SELECT stationid,stationname,stroad FROM myself_station WHERE stationname like '%$st%' And stationname<>'$st' And isdelete=0 order by stationname");
		$stations=array();
		$count=$db->num_rows($query);
		if($count)
		{			
			$ismore=true;
			$cols=5;
			while ($record=$db->fetch_array($query))
			{				
				$stations[]=$record;
			}
			//������
			$record=array('','');		
			while ($count%$cols!=0)
			{
				$stations[]=$record;
				$count++;
			}
		}
	}
	unset($query);
	
}
else if($rd!='')
{
	//��ѯ��·
	$st=$rd;
    $st=trim($st);
	if(!strpos($st,'·'))
	$head['title']=$st.'·-'.$head['title'];
	
	$isstation=true;	//������ʾ��־Ϊtrue	
	
	$lines=array();
	
	$r2=$db->get_one("select count(*) as num from myself_bus where locate('$st', road)<>0");
	$total=$r2['num'];
   
	if($total)   //�ҵ���վ��
	{
		unset($r2);		
		$isfind=true;
		$query=$db->query("SELECT * FROM myself_bus WHERE LOCATE( '$st', road) <>0 ORDER BY id limit $offset,$pagesize");
		while($record=$db->fetch_array($query))   
		{			
			$record['updatetime']=date('Y-m-d',$record['updatetime']);
       		$record['attribute']=trim($record['attribute']);
       			
       		if(strpos($record['attribute'],' '))
       		{
       			$record['att1']=substr($record['attribute'],0,strpos($record['attribute'],' '));
       			$record['attribute']=trim(substr($record['attribute'],strpos($record['attribute'],' ')+1));
       		}
       		
       		if(strpos($record['attribute'],' '))
       		{
        		$record['att2']=substr($record['attribute'],0,strpos($record['attribute'],' '));
        		$record['attribute']=trim(substr($record['attribute'],strpos($record['attribute'],' ')+1));
        		
        		if(strpos($record['attribute'],' '))
        		{
        			$record['attribute']=str_replace(' ','<br>',$record['attribute']);
        		}
        	}
        	else
        	{
        		$record['att2']=$record['attribute'];
        		$record['attribute']='';
        	}	
			$lines[]=$record;	
		}
		$url='?st='.urlencode($st);
		$pagehtml=pageshtml($total,$page,$pagesize,$url);
		
		//��ȡ��·��ͼ����Ϣ����������
		//$r=$db->get_one("select * from myself_station where stationname='$st' and isdelete='0'");		
		
	}
	else 
	{
		$isfind=false;		
	}
	unset($query);
}
else if($bus!='')
{
	//��·��ѯ
    $bus=trim($bus);	

	if(!strpos($bus,'·'))
	  $head['title']=$bus.'·-'.$head['title'];
    else
	  $head['title']=$bus.'-'.$head['title'];

	$isline=true;	//������ʾ��־Ϊtrue
	
	if(!strpos($bus,'·')&& is_numeric($bus))
	{
		//$bus=substr($bus,0,strpos($bus,'·'));   //��ȡ��·��ǰ����ַ�
		$bus.='·';
	}
	$stations=array();
	$query=$db->query("SELECT id, name, attribute, remarks, road, updatetime,editor FROM myself_bus WHERE name LIKE '$bus'");
	if($db->num_rows($query))   //�ҵ���·��
	{
		$isfind=true;
		extract($db->fetch_array($query));
		$updatetime=date('Y-m-d',$updatetime);
		$attribute=trim($attribute);
		$att1=substr($attribute,0,strpos($attribute,' '));
		$attribute=trim(substr($attribute,strpos($attribute,' ')+1));
                if(strpos($attribute,' '))
                {
		$att2=substr($attribute,0,strpos($attribute,' '));
		$attribute=trim(substr($attribute,strpos($attribute,' ')+1));
                }
                else
                {
                $att2=$attribute;
                $attribute='';
                }
                if($attribute!='')
		$attribute=str_replace(' ','<br>',$attribute);
		
		if($id)
		{
			$stations=get_stations($id);
			$all=count($stations);
		}
		
		
		//������ع�����·
		$lines=array();
		$query=$db->query("SELECT id, name, updatetime FROM myself_bus WHERE id<>'$id' and name LIKE '%".$bus."%' order by id");
		$count=$db->num_rows($query);
		if($count)
		{		
			$ismore=true;	
			$cols=6;
			while ($record=$db->fetch_array($query))
			{
				$record['updatetime']=date('Y-m-d',$record['updatetime']);
				$lines[]=$record;
			}
			//������
			$record=array('','','');		
			while ($count%6!=0)
			{
				$lines[]=$record;
				$count++;
			}
		}
		//$bus=$bus.'·';
	}
	else 
	{
		$isfind=false;
		//������ع�����·
		$lines=array();
		$query=$db->query("SELECT id, name, updatetime FROM myself_bus WHERE name LIKE '%".$bus."%' order by id");
		$count=$db->num_rows($query);
		if($count==0)
		{
			$ismore=true;
			$cols=6;
			while ($record=$db->fetch_array($query))
			{
				$record['updatetime']=date('Y-m-d',$record['updatetime']);
				$lines[]=$record;
			}
			//������
			$record=array('','','');		
			while ($count%6!=0)
			{
				$lines[]=$record;
				$count++;
			}
		}
	}	
	
		
}


//��·���˲��ִ���

else if($station1!='' && $station2!='')
{
	//վվ��ѯ	
// վ��һ��س���
  set_time_limit(0);
	$station1=trim($station1);
	$station2=trim($station2);
	
	$lines=array();
	$r=$db->get_one("select count(*) as num FROM myself_busline AS a INNER JOIN myself_busline AS b ON a.busid = b.busid WHERE a.stationname = '$station1' AND a.isdelete='0' 
	AND b.stationname = '$station2' AND b.isdelete='0' AND ( (a.symbol='0' and b.symbol='0') or (a.sequence<b.sequence and a.symbol='1' and b.symbol<>'2') or 
	(a.sequence > b.sequence and a.symbol='2' and b.symbol<>'1') or (a.sequence < b.sequence and a.symbol='0' and b.symbol='1') or 
	(a.sequence > b.sequence and a.symbol='0' and b.symbol='2') )");
	$total=$r['num'];
	if($total)
	{			
	$query=$db->query("SELECT c.*, a.symbol AS symbol1, a.sequence AS sequence1, b.symbol AS symbol2, b.sequence AS sequence2 
	FROM myself_busline AS a INNER JOIN myself_busline AS b ON a.busid = b.busid INNER JOIN myself_bus AS c ON a.busid=c.id and b.busid=c.id 
	WHERE a.stationname = '$station1' AND a.isdelete='0' AND b.stationname = '$station2' AND b.isdelete='0' AND ((a.symbol='0' and b.symbol='0') or 
	(a.sequence<b.sequence and a.symbol='1' and b.symbol<>'2') or (a.sequence > b.sequence and a.symbol='2' and b.symbol<>'1') or 
	(a.sequence < b.sequence and a.symbol='0' and b.symbol='1') or (a.sequence > b.sequence and a.symbol='0' and b.symbol='2')) 
	ORDER BY c.name limit $offset,$pagesize");

	while ($record=$db->fetch_array($query))  //ֱ�﹫��
	{		
		    $record['updatetime']=date('Y-m-d',$record['updatetime']);
       	$record['attribute']=trim($record['attribute']);
       	if(strpos($record['attribute'],' '))
       	{
			$record['att1']=substr($record['attribute'],0,strpos($record['attribute'],' '));		
       		$record['attribute']=trim(substr($record['attribute'],strpos($record['attribute'],' ')+1));
       	}
       	if(strpos($record['attribute'],' '))
       	{
       		$record['att2']=substr($record['attribute'],0,strpos($record['attribute'],' '));
       		$record['attribute']=trim(substr($record['attribute'],strpos($record['attribute'],' ')+1));
       		if(strpos($record['attribute'],' '))
       		{
       			$record['attribute']=str_replace(' ','<br>',$record['attribute']);
       		}
       	}
       	else 
       	{
       		$record['att2']=$record['attribute'];
       		$record['attribute']='';
       	}
       	$lines[]=$record;
		
	}
		$isfind=true;
		$url='?station1='.$station1.'&station2='.$station2;
		$pagehtml=pageshtml($total,$page,$pagesize,$url);
	
	}
	else 
	{
		$isfind=false;
		//Ҫ���ж�վ���Ƿ���ȷ���Ƿ����
		$query=$db->query("Select stationid From myself_station Where isdelete='0' And stationname='$station1'");
		if($db->num_rows($query))
		{
			//������
			$query=$db->query("Select stationid From myself_station Where isdelete='0' And stationname='$station2'");
			if($db->num_rows($query))
			{
				//�յ�Ҳ���ڣ���ѯת������
				$istwo=true;
				$station1_links=array();
				$station2_links=array();
				$query=$db->query("Select stationid, stationname From myself_station Where isdelete='0' And (stationname like '%$station1%' Or locate( stationname, '$station1' ) <>0) And stationname<>'$station1' Order by stationid");
				if($db->num_rows($query))
				{
					while ($station1_links[]=$db->fetch_array($query)){}
				}				
				if($station1_links){ $is_s1_links=true;}
				
				$query=$db->query("Select stationid, stationname From myself_station Where isdelete='0' And (stationname like '%$station2%'  Or locate( stationname, '$station2' ) <>0) And stationname<>'$station2' Order by stationid");
				if($db->num_rows($query))
				{
					while ($station2_links[]=$db->fetch_array($query)){}
				}	
				if($station2_links){ $is_s2_links=true;}
				


 $sql1 = "SELECT a.*,b.name FROM `myself_busline` as a INNER JOIN myself_bus as b ON a.busid = b.id 
 WHERE a.stationname='$station1'";
 $result = mysql_query($sql1) or die(mysql_error() . __LINE__);

 $station1Array = array();
 while ($rowStation1 = mysql_fetch_assoc($result)) {
	$station1Array[$rowStation1['id']] = $rowStation1;
 }

 $bus1Array = array();
 // ����һ���վ���ҳ���
 foreach ($station1Array as $key => $value) {
 $sql = "SELECT m.stationid as stationid1, m.stationname as stationname1,m.sequence as sequence1,e.stationid as stationid2,
 e.stationname as stationname2,e.sequence as sequence2,ABS(e.sequence - m.sequence) as count1 FROM `myself_busline` as m
 INNER JOIN `myself_station` as a ON m.stationid = a.stationid 
 INNER JOIN `myself_busline` as e ON m.busid = e.busid 
 LEFT JOIN `myself_busline` as n ON m.busid = n.busid
 WHERE (a.relation1 is not null OR a.relation2 is not null OR a.relation3 is not null) AND m.busid={$value['busid']} 
 AND m.isdelete=0 AND e.stationname='$station1' AND ((m.symbol='0' AND n.symbol='0') OR 
 (n.sequence<m.sequence AND n.symbol='1' AND m.symbol<>'2') OR 
 (n.sequence > m.sequence AND n.symbol='2' AND m.symbol<>'1') OR
 (n.sequence < m.sequence AND n.symbol='0' AND m.symbol='1') OR 
 (n.sequence > m.sequence AND n.symbol='0' AND m.symbol='2')) ";
 $result = mysql_query($sql) or die(mysql_error() . __LINE__);
	while ($bus1row = mysql_fetch_assoc($result))
	{
  	$bus1Array[$value['name']][$bus1row['stationid2']] = $bus1row['stationname2'];
		$bus1Array[$value['name']][$bus1row['stationname2']] = $bus1row['sequence2'];
		$bus1Array[$value['name']][$bus1row['stationid1']] = $bus1row['stationname1'];
		$bus1Array[$value['name']][$bus1row['stationname1']] = $bus1row['sequence1'];
		$bus1Array[$value['name']][count1] = $bus1row['count1'];
	}
}

// վ�����س���
$sql2 = "SELECT a.*,b.name FROM `myself_busline` as a INNER JOIN myself_bus as b ON a.busid = b.id 
WHERE a.stationname='$station2'";
$result = mysql_query($sql2) or die(mysql_error() . __LINE__);

$station2Array = array();
while ($rowStation2 = mysql_fetch_assoc($result))
{
	$station2Array[$rowStation2['id']] = $rowStation2;
}

$bus2Array = array();
// ����һ���վ���ҳ���
foreach ($station2Array as $key => $value) {
$sql = "SELECT m.stationid as stationid1, m.stationname as stationname1,m.sequence as sequence1,e.stationid as stationid2,
e.stationname as stationname2,e.sequence as sequence2,ABS(e.sequence - m.sequence) as count2 FROM `myself_busline` as m
INNER JOIN `myself_station` as a ON m.stationid = a.stationid 
INNER JOIN `myself_busline` as e ON m.busid = e.busid 
LEFT JOIN `myself_busline` as n ON m.busid = n.busid
WHERE (a.relation1 is not null OR a.relation2 is not null OR a.relation3 is not null) AND m.busid={$value['busid']} 
AND m.isdelete=0 AND e.stationname='$station2' AND ((m.symbol='0' AND n.symbol='0') OR 
(m.sequence<n.sequence AND m.symbol='1' AND n.symbol<>'2') OR 
(m.sequence > n.sequence AND m.symbol='2' AND n.symbol<>'1') OR
(m.sequence < n.sequence AND m.symbol='0' AND n.symbol='1') OR 
(m.sequence > n.sequence AND m.symbol='0' AND n.symbol='2')) ";
	$result = mysql_query($sql) or die(mysql_error() . __LINE__);
	while ($bus2row = mysql_fetch_assoc($result))
	{
		$bus2Array[$value['name']][$bus2row['stationid1']] = $bus2row['stationname1'];
		$bus2Array[$value['name']][$bus2row['stationname1']] = $bus2row['sequence1'];
		$bus2Array[$value['name']][$bus2row['stationid2']] = $bus2row['stationname2'];
		$bus2Array[$value['name']][$bus2row['stationname2']] = $bus2row['sequence2'];
		$bus2Array[$value['name']][count2] = $bus2row['count2'];
	}
}

$changeBus = array();
if (count($bus1Array) > 0 && count($bus2Array) > 0) {
	foreach ($bus1Array as $bus1_key1 => $bus1_value1) {
		foreach ($bus1_value1 as $bus1_station) {
			foreach ($bus2Array as $bus2_key1 => $bus2_value1) {
				foreach ($bus2_value1 as $bus2_station) {
			  $sql = "SELECT stationid AS can FROM `myself_station` WHERE stationname='{$bus1_station}' AND 
			  (relation1='{$bus2_station}' OR relation2='{$bus2_station}' OR relation3='{$bus2_station}') ";
			  $result = mysql_query($sql) or die(mysql_error());
			  if (mysql_num_rows($result))
//                 ��һ����    ����ĵ�һ��վ��   ����վ����    �ڶ���վ��   �ڶ�����   ����վ����   ��վ���� 
$changeBus[]=array($bus1_key1,$bus1_station,$bus1_value1[count1],$bus2_station,$bus2_key1,$bus2_value1[count2],$bus1_value1[count1]+$bus2_value1[count2]); 
				}
			}
		}
	}
}

foreach ($changeBus as $key => $row) {
    $firstst[$key]  = $row[0];
    $totalst[$key] = $row[6];
}

//array_multisort($firstst, SORT_ASC, $totalst, SORT_ASC, $changeBus);���������ȷ����
array_multisort($totalst,SORT_ASC,$firstst, SORT_ASC,$changeBus);
$changeBus = array_slice($changeBus, 0, 10); //ֻ��ʾ10����·���˵ķ�������
//array_multisort($totalst, SORT_ASC, $changeBus); 
/*echo "<pre>";var_dump($changeBus);echo "</pre>";*/
//array_multisort($changeBus[0],SORT_ASC,SORT_STRING,$changeBus[6],SORT_NUMERIC,SORT_DESC);

				if ($changeBus)  
				{$is_changeBus=true;}

			}
			else 
			{
				//�����ڣ��յ㲻����				
				$query=$db->query("Select stationname From myself_station Where isdelete='0' And (stationname like '%$station2%'  Or locate( stationname, '$station2' ) <>0) Order by stationid");
				if($db->num_rows($query))
				{
					$count=$db->num_rows($query);
					$station2_stat=array();
					while ($record=$db->fetch_array($query))
					{
						$station2_stat[]=$record;
					}
					$is_s2_stat=true;	
					$record=array('');
					while ($count%3!=0)
					{
						$station2_stat[]=$record;						
						$count++;
					}		
				}
				else 
				{
					$isnone=true;
				}
				unset($query);
			}
		}
		else 
		{
			//��㲻���ڴ��ڣ��ж��յ��Ƿ����
			$query=$db->query("Select stationid From myself_station Where isdelete='0' And stationname='$station2'");
			if($db->num_rows($query))
			{
				//�յ���ڣ���ѯ
				$query=$db->query("Select stationname From myself_station Where isdelete='0' And (stationname like '%$station1%'  Or locate( stationname, '$station1' ) <>0) Order by stationid");
				if($db->num_rows($query))
				{
					$count=$db->num_rows($query);
					$station1_stat=array();
					while ($record=$db->fetch_array($query))
					{
						$station1_stat[]=$record;
					}
					$is_s1_stat=true;	
					$record=array('');
					while ($count%3!=0)
					{
						$station1_stat[]=$record;						
						$count++;
					}		
				}
				else 
				{
					$isnone=true;
				}			
			}
			else 
			{
				//��㡢�յ㲻���ڣ���ѯ��㡢�յ����վ�㣬��������ʾ��㡢�յ������Ϣ��������ʾ��ѯ������׼ȷ
				$query=$db->query("Select stationid, stationname From myself_station Where isdelete='0' And (stationname like '%$station1%'  Or locate( stationname, '$station1' ) <>0) Order by stationid");
				if($db->num_rows($query))
				{
					$count1=$db->num_rows($query);
					$station1_stat=array();
					$i=0;
					while ($record=$db->fetch_array($query))
					{
						$station1_stat[]=$record;
					}						
				}
				
				$query=$db->query("Select stationid, stationname From myself_station Where isdelete='0' And (stationname like '%$station2%'  Or locate( stationname, '$station2' ) <>0) Order by stationid");
				if($db->num_rows($query))
				{
					$count2=$db->num_rows($query);
					$station2_stat=array();
					while ($record=$db->fetch_array($query))
					{
						$station2_stat[]=$record;
					}							
				}
				if($station1_stat || $station2_stat)
				{
					$isRelative=true;
					while (count($station1_stat) > count($station2_stat))
					{
						$record=array('','');
						$station2_stat[]=$record;
					}				
					while (count($station1_stat) < count($station2_stat))
					{
						$record=array('','');
						$station1_stat[]=$record;
					}
				}
				else 
				{
					$isRelative=false;
				}
			}
			unset($query);
		}
		
	}	
	
}








else if($s1!='' && $s2!='')
{
	//վվ��ѯ	
	$s1=trim($s1);
	$s2=trim($s2);
	
	$lines=array();
	$r=$db->get_one("select count(*) as num FROM myself_busline AS a INNER JOIN myself_busline AS b ON a.busid = b.busid WHERE a.stationname = '$s1' AND a.isdelete='0' 
	AND b.stationname = '$s2' AND b.isdelete='0' AND ( (a.symbol='0' and b.symbol='0') or (a.sequence<b.sequence and a.symbol='1' and b.symbol<>'2') or 
	(a.sequence > b.sequence and a.symbol='2' and b.symbol<>'1') or (a.sequence < b.sequence and a.symbol='0' and b.symbol='1') or 
	(a.sequence > b.sequence and a.symbol='0' and b.symbol='2') )");
	$total=$r['num'];
	if($total)
	{			
	$query=$db->query("SELECT c.*, a.symbol AS symbol1, a.sequence AS sequence1, b.symbol AS symbol2, b.sequence AS sequence2 
	FROM myself_busline AS a INNER JOIN myself_busline AS b ON a.busid = b.busid INNER JOIN myself_bus AS c ON a.busid=c.id and b.busid=c.id 
	WHERE a.stationname = '$s1' AND a.isdelete='0' AND b.stationname = '$s2' AND b.isdelete='0' AND ((a.symbol='0' and b.symbol='0') or 
	(a.sequence<b.sequence and a.symbol='1' and b.symbol<>'2') or (a.sequence > b.sequence and a.symbol='2' and b.symbol<>'1') or 
	(a.sequence < b.sequence and a.symbol='0' and b.symbol='1') or (a.sequence > b.sequence and a.symbol='0' and b.symbol='2')) 
	ORDER BY c.name limit $offset,$pagesize");

	while ($record=$db->fetch_array($query))  //ֱ�﹫��
	{		
		$record['updatetime']=date('Y-m-d',$record['updatetime']);
       	$record['attribute']=trim($record['attribute']);
       	if(strpos($record['attribute'],' '))
       	{
			$record['att1']=substr($record['attribute'],0,strpos($record['attribute'],' '));		
       		$record['attribute']=trim(substr($record['attribute'],strpos($record['attribute'],' ')+1));
       	}
       	if(strpos($record['attribute'],' '))
       	{
       		$record['att2']=substr($record['attribute'],0,strpos($record['attribute'],' '));
       		$record['attribute']=trim(substr($record['attribute'],strpos($record['attribute'],' ')+1));
       		if(strpos($record['attribute'],' '))
       		{
       			$record['attribute']=str_replace(' ','<br>',$record['attribute']);
       		}
       	}
       	else 
       	{
       		$record['att2']=$record['attribute'];
       		$record['attribute']='';
       	}
       	$lines[]=$record;
		
	}
		$isfind=true;
		$url='?s1='.$s1.'&s2='.$s2;
		$pagehtml=pageshtml($total,$page,$pagesize,$url);
	
	}
	else 
	{
		$isfind=false;
		//Ҫ���ж�վ���Ƿ���ȷ���Ƿ����
		$query=$db->query("Select stationid From myself_station Where isdelete='0' And stationname='$s1'");
		if($db->num_rows($query))
		{
			//������
			$query=$db->query("Select stationid From myself_station Where isdelete='0' And stationname='$s2'");
			if($db->num_rows($query))
			{
				//�յ�Ҳ���ڣ���ѯת������
				$istwo=true;
				$s1_links=array();
				$s2_links=array();
				$query=$db->query("Select stationid, stationname From myself_station Where isdelete='0' And (stationname like '%$s1%' Or locate( stationname, '$s1' ) <>0) And stationname<>'$s1' Order by stationid");
				if($db->num_rows($query))
				{
					while ($s1_links[]=$db->fetch_array($query)){}
				}				
				if($s1_links){ $is_s1_links=true;}
				
				$query=$db->query("Select stationid, stationname From myself_station Where isdelete='0' And (stationname like '%$s2%'  Or locate( stationname, '$s2' ) <>0) And stationname<>'$s2' Order by stationid");
				if($db->num_rows($query))
				{
					while ($s2_links[]=$db->fetch_array($query)){}
				}	
				if($s2_links){ $is_s2_links=true;}
				
				$ways=array();
				$query=$db->query("SELECT m. * , n.name AS name2 FROM ( SELECT m. * , n.name AS name1 FROM 
				( SELECT ABS( a.sequence - b.sequence ) AS count1, ABS( c.sequence - d.sequence ) AS count2, 
				ABS( a.sequence - b.sequence ) + ABS( c.sequence - d.sequence ) AS count, a.busid AS line1, b.stationname, b.stationid, c.busid AS line2
				FROM myself_busline AS a INNER JOIN myself_busline AS b ON a.busid = b.busid AND a.stationname <> b.stationname INNER JOIN myself_busline AS c 
				ON b.stationname = c.stationname INNER JOIN myself_busline AS d ON c.busid = d.busid AND c.stationname <> d.stationname 
				WHERE a.stationname = '$s1' AND d.stationname = '$s2' AND a.symbol = '0' AND b.symbol = '0' AND c.symbol = '0' AND d.symbol = '0' 
				And a.isdelete='0' And b.isdelete='0' And c.isdelete='0' And d.isdelete='0' ) AS m, myself_bus AS n WHERE m.line1 = n.ID ) AS m, myself_bus AS n 
			WHERE m.line2 = n.ID ORDER BY m.name1, name2, m.count");
			//	WHERE m.line2 = n.ID ORDER BY count,m.stationname limit 0,20");
			//WHERE m.line2 = n.ID ORDER BY m.name1, name2, m.count limit $offset,$pagesize");
	
	//���յ�һ�γ˳�����·�͵ڶ��γ˳�����·���в�����վ�������ٵ������У���2��·��ͬ������£�ֻ����һ��վ�����ٷ�����
    if($db->num_rows($query))
    {
     $carStateOne = '';$carStateTwo = '';
     while ($ways1=$db->fetch_array($query))
     {
       if ($ways1['name1'] == $carStateOne && $ways1['name2'] == $carStateTwo){
        //die('the same car ' . $carStateOne . ' And ' . $carStateTwo);
        continue; 
       }
        
      $carStateOne = $ways1['name1'];
      $carStateTwo = $ways1['name2'];
      $ways[] = $ways1;
      $total = count($ways);
      }  
      //print_r($ways);//exit();
       foreach ($ways as $key => $row) {
           $counttotal[$key]  = $row[2];
          }

     array_multisort($counttotal,SORT_ASC,$ways);
     unset($carStateOne, $carStateTwo);
     $isturn=true; 
    // $url='?s1='.$s1.'&s2='.$s2;
	   //$pagehtml=pageshtml($total,$page,$pagesize,$url); 

    }
				
		/*	if($db->num_rows($query))//ԭ��δʵ��ɾ���ظ���¼�Ĵ��루2����·��ͬ��
				{									
					while ($ways[]=$db->fetch_array($query))
					{}	
					$isturn=true;	  	
				}*/
				
				else 
				{
					$isturn=false;
				}
				unset($query);
			}
			else 
			{
				//�����ڣ��յ㲻����				
				$query=$db->query("Select stationname From myself_station Where isdelete='0' And (stationname like '%$s2%'  Or locate( stationname, '$s2' ) <>0) Order by stationid");
				if($db->num_rows($query))
				{
					$count=$db->num_rows($query);
					$s2_stat=array();
					while ($record=$db->fetch_array($query))
					{
						$s2_stat[]=$record;
					}
					$is_s2_stat=true;	
					$record=array('');
					while ($count%3!=0)
					{
						$s2_stat[]=$record;						
						$count++;
					}		
				}
				else 
				{
					$isnone=true;
				}
				unset($query);
			}
		}
		else 
		{
			//��㲻���ڴ��ڣ��ж��յ��Ƿ����
			$query=$db->query("Select stationid From myself_station Where isdelete='0' And stationname='$s2'");
			if($db->num_rows($query))
			{
				//�յ���ڣ���ѯ
				$query=$db->query("Select stationname From myself_station Where isdelete='0' And (stationname like '%$s1%'  Or locate( stationname, '$s1' ) <>0) Order by stationid");
				if($db->num_rows($query))
				{
					$count=$db->num_rows($query);
					$s1_stat=array();
					while ($record=$db->fetch_array($query))
					{
						$s1_stat[]=$record;
					}
					$is_s1_stat=true;	
					$record=array('');
					while ($count%3!=0)
					{
						$s1_stat[]=$record;						
						$count++;
					}		
				}
				else 
				{
					$isnone=true;
				}			
			}
			else 
			{
				//��㡢�յ㲻���ڣ���ѯ��㡢�յ����վ�㣬��������ʾ��㡢�յ������Ϣ��������ʾ��ѯ������׼ȷ
				$query=$db->query("Select stationid, stationname From myself_station Where isdelete='0' And (stationname like '%$s1%'  Or locate( stationname, '$s1' ) <>0) Order by stationid");
				if($db->num_rows($query))
				{
					$count1=$db->num_rows($query);
					$s1_stat=array();
					$i=0;
					while ($record=$db->fetch_array($query))
					{
						$s1_stat[]=$record;
					}						
				}
				
				$query=$db->query("Select stationid, stationname From myself_station Where isdelete='0' And (stationname like '%$s2%'  Or locate( stationname, '$s2' ) <>0) Order by stationid");
				if($db->num_rows($query))
				{
					$count2=$db->num_rows($query);
					$s2_stat=array();
					while ($record=$db->fetch_array($query))
					{
						$s2_stat[]=$record;
					}							
				}
				if($s1_stat || $s2_stat)
				{
					$isRelative=true;
					while (count($s1_stat) > count($s2_stat))
					{
						$record=array('','');
						$s2_stat[]=$record;
					}				
					while (count($s1_stat) < count($s2_stat))
					{
						$record=array('','');
						$s1_stat[]=$record;
					}
				}
				else 
				{
					$isRelative=false;
				}
			}
			unset($query);
		}
		
	}	
	
}
else   //��ҳ�г����й���·��
{	
	$allline=true;  //����ģ�����ʾΪ���й����б�
	$cols=7;        //�е���Ŀ
	$lines=array();
	$query=$db->query("SELECT id, name, updatetime FROM myself_bus ORDER BY name");
	while ($record=$db->fetch_array($query))
	{
		$record['updatetime']=date('Y-m-d',$record['updatetime']);
		$lines[]=$record;
	}
	//������
	$record=array('','','');
	$count=$db->num_rows($query);
	while ($count%7!=0)
	{
		$lines[]=$record;
		$count++;
	}
}

include template('phpcms', 'bus_index');

//��ҳ����
function pageshtml($total, $page = 1, $perpage = 10, $url = '')
{
	global $PHP_URL,$LANG;
	if(!$url) $url = preg_replace("/(.*)([&?]page=[0-9]*)(.*)/i", "\\1\\3", $PHP_URL);
	$s = strpos($url, '?') === FALSE ? '?' : '&';
	$pages = ceil($total/$perpage);
	$page = min($pages,$page);
	$prepg = $page-1;
	$nextpg = $page == $pages ? 0 : ($page+1);
	if($total < 1) return FALSE;
	$pagenav = $LANG['total']."<b>$total</b>&nbsp;&nbsp;&nbsp;&nbsp;";
	$pagenav .= $prepg ? "<a href='$url{$s}page=1'>".$LANG['first']."</a>&nbsp;<a href='$url{$s}page=$prepg'>".$LANG['previous']."</a>&nbsp;" : $LANG['first']."&nbsp;".$LANG['previous']."&nbsp;";
	$pagenav .= $nextpg ? "<a href='$url{$s}page=$nextpg'>".$LANG['next']."</a>&nbsp;<a href='$url{$s}page=$pages'>".$LANG['last']."</a>&nbsp;" : $LANG['next']."&nbsp;".$LANG['last']."&nbsp;";
	$pagenav .= $LANG['page'].": <b><font color=red>$page</font>/$pages</b>&nbsp;&nbsp;<input type='text' name='page' id='page' class='page' size='2' onKeyDown=\"if(event.keyCode==13) {window.location='{$url}{$s}page='+this.value; return false;}\"> <input type='button' value='GO' class='gotopage' style='width:30px' onclick=\"window.location='{$url}{$s}page='+document.getElementById('page').value\">\n";
	return $pagenav;
}

function get_stations($busId)
{
	global $db;
	$stations=array();
	$query=$db->query("SELECT a.stationid, a.symbol, a.sequence, b.stationname, b.stroad FROM myself_busline a, myself_station b 
	WHERE a.isdelete=0 and a.busid='$busId' and a.stationid=b.stationid and b.isdelete='0' order by a.sequence");
	while ($record=$db->fetch_array($query))
	{
		$stations[]=$record;
	}
	return $stations;
}

function get_stationID($stationname)
{
	global $db;	
	$r=$db->get_one("SELECT stationid FROM myself_station WHERE isdelete=0 and stationname='$stationname'");
	if($r['stationid'])
	{
		return $r['stationid'];
	}
	else 
	{
		return 0;
	}
}

function get_maxSequence($busId)
{
	global $db;
	$r=$db->get_one("SELECT max( sequence ) as num FROM myself_busline WHERE busid = '$busId' AND isdelete =0");
	return $r['num']; 
}
?>