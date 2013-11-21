<?php
error_reporting(0);
include_once "conn.php";
$click = $_GET['click'];
$member_id = $_GET['member_id'];

function login(){
	echo "<form id='form1' name='form1' method='get' action='?'>
  <table align='center' width='200' border='1'>
    <tr>
      <td>Member ID</td>
      <td><input type='text' name='member_id' /></td>
    </tr>
    <tr>
      <td colspan='2'><input type='submit' value='Log in'/></td>
    </tr>
  </table>
</form>";
	}// end login
	
function menu($member_id){
	echo "
	<table align='center'  width='300' border='1'>
  <tr>
    <td><a href='?click=add&member_id=$member_id'>Add CPDs</a></td>
    <td><a href='?click=view&member_id=$member_id'>View CPDs</a></td>
    <td><a href='?'>Log out</a></td>
  </tr>
</table>
	";
	}//end menu
		
function add($member_id, $click ='add',$id_list=null){

	echo "
	<form id='form2' name='form2' method='post' action='?'>
	<input type='hidden' name='member_id' value='$member_id'/>
 <table align='center' border='1'>
    <tr>
      <th>Provider </th>
      <th>Start Date </th>
      <th>End Date </th>
      <th>CPD Hours Attained </th>
      <th>Description[theme/subjects/topics]</th>
    </tr>";
	if($click =='Update'){		
	$sql=mysql_query ("SELECT * FROM cpd_details WHERE id IN ($id_list)");						
		while($row = mysql_fetch_array($sql)){
			echo"<tr>
			  <input type='hidden' name='id[]' value='$row[0]' />
			  <td><input type='text' name='provider[]' value='$row[2]' /></td>
			  <td><input type='text' name='start_date[]' placeholder='YYYY-MM-DD' value='$row[3]'/></td>
			  <td><input type='text' name='end_date[]' placeholder='YYYY-MM-DD' value='$row[4]' /></td>
			  <td><input type='text' name='cpd_hours[]' value='$row[5]' /></td>
			  <td><textarea name='description[]' cols='45' rows='1'  >$row[6]</textarea></td>
			</tr>
			";
		 }// end while
        echo"
			<tr><td colspan='2'>&nbsp;</td>
			<td colspan='3'><input name='update' type='submit' value='Update'/></td>
			</tr>";
		}// end if 
		
	if($click == 'add'){
	for ($i=0; $i<4; $i++){
		echo"<tr>
     		 <td><input type='text' name='provider[]' /></td>
     		 <td><input type='text' name='start_date[]' placeholder='YYYY-MM-DD' /></td>
      		 <td><input type='text' name='end_date[]' placeholder='YYYY-MM-DD' /></td>
      		 <td><input type='text' name='cpd_hours[]' /></td>
      		 <td><textarea name='description[]' cols='45' rows='1'></textarea></td>
    		 </tr>";
		}// end for 
        echo"
	 		 <tr><td colspan='2'><input name='save' type='submit' value='Save'/></td>
      		 <td colspan='3'><input name='save' type='submit' value='Save Add'/></td></tr>";
	   }// end if
	echo "
  </table>
</form>";
	}// end add cpds
	
	function view($member_id){
		$sql = mysql_query("SELECT * FROM cpd_details WHERE member_id = '$member_id'");
		if(mysql_num_rows($sql)>0){
			echo "<form action='?' method='post' >
			<input type='hidden' name='member_id' value='$member_id'/>
			<table border=1 align='center'><tr><th>Provider </th><th>Start Date </th> <th>End Date </th><th>CPD Hours Attained </th><th>Description[theme/subjects/topics]</th><th>Modify</th></tr>";
			
			while ($row = mysql_fetch_array($sql)){
					$id = $row[0];
					$provider_institution = $row[2];
					$start_date 	 = $row[3];
					$end_date = $row[4];
					$cpd_hours = $row[5];
					$description = $row[6];
					$approved= $row[7];
					
					echo"<tr><td>$provider_institution</td><td>$start_date</td><td>$end_date</td><td>$cpd_hours</td><td>$description</td><td align='center'>";if ($approved==1){echo "Approved";} if ($approved==0){echo "<input name='id[]' type ='checkbox' value = '$id'>";} echo"</td></tr>";
				}// end while
				
			echo "<tr><td colspan='5'> </td>
					<td><input type='submit' name='save' value = 'Update'>:::<input type='submit' name='save' value = 'Delete'> </td>
				    </tr>
				</table></form>";
				
			}//end if 
		}//end view 
	
	if(!empty($_GET['login'])){
		
		$member_id=str_replace("'",'`',trim($_GET['member_id']));
		$sql = mysql_query("SELECT * FROM member_details WHERE member_id = '$member_id'");

		if(mysql_num_rows($sql)==0){
			mysql_query("INSERT INTO member_details (member_id)VALUES ('$member_id')");
			}// end if
			
		}// end if 
				
		
	if(!empty($_POST['save'])){
		$id =$_POST['id'];
		$provider =  $_POST['provider'];
		$start_date =  $_POST['start_date'];
		$end_date =  $_POST['end_date'];
		$cpd_hours =  $_POST['cpd_hours'];
		$description =  $_POST['description'];
		$member_id = $_POST['member_id'];
		$save = $_POST['save'];
		

		
		if($save == 'Save Add' || $save == 'Save' ){
		while(list($key,$value) = each($provider)){
			 $provider_value  = $value;
			 $start_date_value  = $start_date[$key];
			 $end_date_value  = $end_date[$key];
			 $cpd_hours_value  = $cpd_hours[$key];
			 $description_value  = $description[$key];
			 
				if (empty($provider_value) or empty($cpd_hours_value)){
					continue;
				}// end if
				
				if($save == 'Save Add' || $save == 'Save'){
				mysql_query("INSERT INTO cpd_details (member_id, provider_institution, start_date, end_date, cpd_hours, description) VALUES ('$member_id', '$provider_value', '$start_date_value', '$end_date_value', '$cpd_hours_value', '$description_value')");
				}// end if
				
			}// end while
			
		}//end if 
		
			if($save== 'Delete' || $save == 'Update'){
				
					while(list($key,$value) = each($id)){
						$id_list  .= "$value,";
					}//end while
					$id_list = trim($id_list, ' ,');
					
					if($save== 'Delete'){
						mysql_query ("DELETE FROM cpd_details WHERE id IN ($id_list)");
					}////end if
					
					if($save == 'Update'){
						$click = 'Update';
					}//end if 
					 	
			}//end if 
		
			if($save == 'Save Add'){
				$click ='add';
				}// end if
				
			if($save == 'Save' || $save== 'Delete'){
				$click ='view';
				}// end if
		
		}// end if
		
	if(!empty($_POST['update'])){
		$id = $_POST['id'];
		$member_id = $_POST['member_id'];
		$provider = $_POST['provider'];
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		$cpd_hours = $_POST['cpd_hours'];
		$description = $_POST['description'];
		
		while(list($key,$value) = each($id)){
			mysql_query("UPDATE cpd_details SET provider_institution= '$provider[$key]',start_date= '$start_date[$key]',end_date= '$end_date[$key]',cpd_hours= $cpd_hours[$key] ,description='$description[$key]' WHERE id=$value");
			}//end while
			$click = 'view';
		}//end if

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Members</title>
</head>
<body>
<?php
if(empty($member_id)){
	login();
	}//end if
	
if(!empty($member_id)){
	menu($member_id);
	}//end if
	
if(($click =='add' || $click =='Update') && !empty($member_id)){
	add($member_id, $click, $id_list);
	}//end if
	
if($click =='view' && !empty($member_id)){
	view($member_id);
	}//end if
	
?>
</body>
</html>