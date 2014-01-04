<?php
session_start();
error_reporting(0);
if (empty($_SERVER['HTTP_REFERER'])){
echo "<p>You need to login first through the ICPAU website!!!";
exit;
}

$_user = $_GET['id'];
$d3_diet = $_GET['d3_diet'];
$d5_diet = $_GET['d5_diet'];
?>

<script>
function validateForm()
{
var box=document.forms["exam_entry"]["box"].value;
var country=document.forms["exam_entry"]["country"].value;
var town=document.forms["exam_entry"]["town"].value;
var mobile=document.forms["exam_entry"]["mobile"].value;
var email=document.forms["exam_entry"]["email"].value;
var date_of_payment= document.forms["exam_entry"]["date_of_payment"].value;
var amount_paid= document.forms["exam_entry"]["amount_paid"].value;


var examination = document.getElementById("examination");
var exemptions = document.getElementById("exemptions");
var annual_renewal = document.getElementById("annual_renewal");

var arua = document.getElementById("Arua");
var Fort_Portal = document.getElementById("Fort Portal");
var Gulu= document.getElementById("Gulu");
var Kampala=document.getElementById("Kampala");
var Mbale=document.getElementById("Mbale");
var Mbarara=document.getElementById("Mbarara");
var declaration=document.getElementById("declaration");

var x=document.forms["exam_entry"]["email"].value;
var atpos=x.indexOf("@");
var dotpos=x.lastIndexOf(".");
if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
  {
  alert("The e-mail address you provided is Not valid");
  return false;
  }

if (box==null || box==""){
  alert("Box must be filled in");
  return false;
  
}else if(country==null || country==""){
  alert("Country must be filled in");
  return false;
		
}else if(town==null || town==""){
  alert("Town must be filled in");
  return false;
		  
}else if(mobile==null || mobile==""){
  alert("Mobile must be filled in");
  return false;
				
}else if(email==null || email==""){
  alert("Email must be filled in");
  return false;
				   
}else if(!(arua.checked || Fort_Portal.checked || Gulu.checked || Kampala.checked || Mbale.checked || Mbarara.checked) ){
  alert("You must choose an examinations centre");
  return false;
							
}else if(date_of_payment ==null || date_of_payment==""){
  alert("You must fill Date of payment");
  return false;
							
}else if(amount_paid ==null || amount_paid==""){
  alert("You must fill Amount Payment");
  return false;
							
}else if(!(exemptions.checked || examination.checked || annual_renewal.checked)){
  alert("You must fill item being Paid for");
  return false;
							
}else if(!(declaration.checked)){
  alert("Declaration must be agreed to prior to submission");
  return false;
}
							  
		
}
</script>
<?php
include_once ('conn.php');
include ('utilities.php');



				// ===== Beginning of form function
				function registration_form($_user=null,$d5_diet=null,$d3_diet=null){
				
				//Database query
				//------> Get all the important attributes of a student from the registration table 
				$sql = mysql_query("SELECT low_eligibility, high_eligibility , concat(low_eligibility,',',high_eligibility) 			all_eligibility,center,initial_reg,colleges,confirmed,initial_date,final_date,final_reg,withdrew, final_modification,course,initial_modified FROM j310_registration WHERE regno = '$_user'");
				$low_eligibility = mysql_result($sql,0,0);
				$high_eligibility = mysql_result($sql,0,1);
				$all_eligibility = mysql_result($sql,0,2);
				$all_eligibility =trim($all_eligibility,',');
 				$center=mysql_result($sql,0,3);
 				$initial_reg=mysql_result($sql,0,4);
 				$registered_colleges = mysql_result($sql,0,5);
				$confirmed = mysql_result($sql,0,6);
				$initial_date = mysql_result($sql,0,7);
				$final_date = mysql_result($sql,0,8);
				$final_reg = mysql_result($sql,0,9);
				$withdrew = mysql_result($sql,0,10);
				$final_modification = mysql_result($sql,0,11);
				$course = mysql_result($sql,0,12);
				$initial_modified = mysql_result($sql,0,13);
				
//query j310_registration_dates
$sql=mysql_query("SELECT actual_date FROM j310_registration_dates");
$d1=mysql_result($sql,0,0);
$d1_string = date('D, j-M-Y',strtotime($d1));

$d2=mysql_result($sql,1,0);
$d2_string = date('D, j-M-Y',strtotime($d2));

$d3=mysql_result($sql,2,0);
$d3_string = date('D, j-M-Y',strtotime($d3));

$d4=mysql_result($sql,3,0);
$d4_string = date('D, j-M-Y',strtotime($d4));

$d5=mysql_result($sql,4,0);
$d5_string = date('D, j-M-Y',strtotime($d5));

$d6=mysql_result($sql,5,0);
$d6_string = date('D, j-M-Y',strtotime($d6));

$d7=mysql_result($sql,6,0);
$d7_string = date('D, j-M-Y',strtotime($d7));



				
				// get address
				$sql= mysql_query("SELECT co,BOX,Town,Country,mobile,email FROM j310_address WHERE regno='$_user'");
			 	$co = mysql_result($sql,0,0);
			 	$box = mysql_result($sql,0,1);
			 	$town = mysql_result($sql,0,2);
			 	$country = mysql_result($sql,0,3);
			 	$mobile = mysql_result($sql,0,4);
			 	$email = mysql_result($sql,0,5);

				$address_string="$co$BOX$Town$Country$mobile$email";

				$sql= mysql_query("SELECT concat(onames,' ',sname) name FROM zeby_students WHERE username='$_user'");
			 	$name= mysql_result($sql,0,0);
				


				banner($_user,$name,$d5_diet);

				//------> Create a flag for a CPA student that have level 3 papers 
				$sql=mysql_query("SELECT 1 FROM j310_registration WHERE regno='$_user' and course = 'CPA' and (low_eligibility LIKE '%13,14%' OR low_eligibility = '14,15' OR low_eligibility = '13,15')");
				$all_papers = mysql_num_rows($sql);
 				 
				//cost computation
				if($all_papers==1 && $initial_reg=='13,14,15'){
					 $cost = 225000;
					 $cost_string='UGX '.substr_replace($cost,',',-3,0).' /= (Exclusive of bank charges)';
					 $_SESSION['cost']=$cost_string;
				}else{
					  $sql=mysql_query("SELECT SUM(cost) FROM j310_icpau_papers WHERE course='$course' AND paper IN ($initial_reg)");
					  $cost = mysql_result($sql,0,0);
					  $cost_string = 'UGX '.substr_replace($cost,',',-3,0).' /= (Exclusive of bank charges)'; 
				}
			
				//end of cost computation
				$registered_colleges_array = explode(',',$registered_colleges);
				$final_reg_array =explode(',',$final_reg);
				$all_eligibility_array =explode(',',$all_eligibility);
												
				
				while(list($key,$val) = each($registered_colleges_array)){
					$get_name = mysql_query("SELECT name FROM trinst WHERE trinst = '$val'");
					$got_name = mysql_result($get_name,0,0);
					$ticked_college_codes_array[] = $val;
					$ticked_college_names_array[] = $got_name;
				} // end while

				//------> Formulate a list of colleges or training institutions 
           		$college_list = '';
				 $sql = mysql_query("SELECT trinst,name FROM trinst WHERE trim(name) != '' ORDER BY name");
				 if(mysql_num_rows($sql)>0){
				 
					 	while($row = mysql_fetch_array($sql)){
							 $college_code = $row['trinst'];
							 $college_name = $row['name'];
							$college_list .= "<option value= '$college_code'> $college_name </option>";
                             
						} // end while
					 } //end if  

				
				 echo "<table width='1043'  height='76' border='0' cellpadding='0' cellspacing='0'>
      <tr>
        <td colspan='2' height='19' style='background-image:url(images/dsms_top.jpg); background-repeat:repeat-x;'></td>
      </tr>
      <tr>
       	<td width='114' height='28' align='left' valign='top'>
    	</td>
    	<td width='929' height='70'  valign='middle' >";
		
		//Messages
		//completely withdrew 
		if(empty($final_reg) and !empty($initial_reg)){
			
			$message="
						<u>Notice:</u> 
			<ul>
			<li>You successfully withdrew from all papers but in case you want to reinstate - contact ICPAU offices before <font color='#FF0000'>$d3_string</font></li>
			</ul>
			";
			echo $message;
			
			$headers = array(
			'From: students@icpau.co.ug',
			'Content-Type: text/html'
			);

			mail($email,'Registration',$message,implode("\r\n",$headers));
			footer($d5_diet);
			exit;
			}
		
		//provisionally registered
		if(!empty($initial_date) and $initial_date==$final_date and $confirmed==0){
		//banner();
			$message="<u><b><font size=3  color='#000099'>Successful Exams Entry Notification:</font></b></u>
			<ul>
			<li>You are provisionally registered for paper(s): <font color='#0000FF'>". $final_reg ."</font> to be sat in <font color='#0000FF'>".$center."</font> examinations centre.</li>
			<li>Your registered exam paper(s) account for <font color='#0000FF'>".$cost_string."</font> that will be confirmed by ICPAU Finance Department.</li>
        	<li>Login by <font color='#0000FF'>".date('D, j-M-Y',strtotime($final_date. ' + 7 days'))."</font>, to see if you have been confirmed.</li>
			</ul>";
			
			echo $message;
			$headers = array(
			'From: students@icpau.co.ug',
			'Content-Type: text/html'
			);

			mail($email,'Registration',$message,implode("\r\n",$headers));
			footer($d5_diet);
			exit;
		}
			
				
		 if($confirmed==1 or ($confirmed == 0 and empty($initial_date))){

        	echo "<ol><form name='exam_entry' action='register.php' method='post' onsubmit='return validateForm()' >
			<input name='all_papers' type='hidden' value='$all_papers' />
			<input name='final_reg' type='hidden' value='$final_reg' />
			<input name='_user' type='hidden' value='$_user' />

			<input name='confirmed' type='hidden' value='$confirmed' />
			<input name='initial_reg' type='hidden' value='$initial_reg' />
			<input name='initial_date' type='hidden' value='$initial_date' />
			<input name='final_date' type='hidden' value='$final_date' />
			<input name='address_string' type='hidden' value='$address_string' />
			<input name='d5_diet' type='hidden' value='$d5_diet' />
			<input name='d3_diet' type='hidden' value='$d3_diet' />
			<input name='center_string' type='hidden' value='$center' />
			<input name='original_college_string' type='hidden' value='$colleges' />
			<input name='low_eligibility' type='hidden' value='$low_eligibility' />
			<input name='all_eligibility' type='hidden' value='$all_eligibility' />
			<input name='high_eligibility' type='hidden' value='$high_eligibility' />
			<input name='course' type='hidden' value='$course' />
			<table border='0' >
			<tr>
			<td colspan='3'>";
				
			if ($confirmed == 0 and empty($initial_date)){
			echo "<u>Notice:</u> 
			<ul>
			<li>ALL fields with (<font  color='#FF0000'>*</font>) are required. </li>
			<li>IN CASE you have any exemptions appearing in the table below - contact ICPAU offices before you proceed to submit this exams entry.</li>
			<li>You OUGHT to have paid before registration OR else your exams entry will be cancelled.</li>
			</ul>";
			}
				
                            
if(isset($_SESSION['error'])){
	echo "<font  color='#FF0000'>".$_SESSION['error']."</font>";
unset($_SESSION['error']);
}else{ 
if($_SESSION['address_modified']){
$message = $_SESSION['address_modified'].'<br>';
unset($_SESSION['address_modified']);
}
							
if($_SESSION['center_modified']){
$message .= $_SESSION['center_modified'].'<br>';
unset($_SESSION['center_modified']);
}
							
if($_SESSION['college_modified']){
$message .= $_SESSION['college_modified'].'<br>';
unset($_SESSION['college_modified']);
}
							
if($confirmed==1){
if($_POST['submit']){
$message .= "<font size=4 color='#00CC00'>Changes saved as indicated in the form below</font><br>";
}
							
$message .="
							<u>Notice:</u> 
			<ul>
			<li>You are successfully confirmed for paper(s): ${final_reg}  </li>
                     
			<li>In case you want to withdraw uncheck the undesired papers</li>
			</ul>
							<br/>";
							}
							 
							
							
							
							echo $message;
							//mail($email,'ICPAU exam registration',$message);
						
                		echo "</td>";
                        
					} //end if
                        $text_size = 30;
					 
            		echo "</tr>
            		<tr>
            		<td colspan='3'><strong><li><u>Your Address Details</u></li></strong>  [Make changes where necessary.]</td>
            		</tr>
            		<tr><td colspan='3'>
   <table border='0' cellspacing='0' cellpadding='2' >
  <tr>
    <td >C/o:</td>
    <td ><input name='co' type='text' size='$text_size' value='$co'></td>
    <td ><font  color='#FF0000'>*</font>Box:</td>
    <td ><input name='box' type='text' size='$text_size' value='$box'></td>
  </tr>
  <tr>
    <td> <font  color='#FF0000'>*</font>Town/City:</td>
    <td><input name='town' type='text' size='$text_size' value='$town'></td>
    <td><font  color='#FF0000'>*</font> Mobile Tel:</td>
    <td><input name='mobile' type='text' size='$text_size' value='$mobile'> eg. 0772-401234</font></td>
  </tr>
  <tr>
    <td><font  color='#FF0000'>*</font> Country:</td>
    <td><input name='country' type='text' size='$text_size' value='$country'></td>
    <td> <font  color='#FF0000'>*</font>Email:</td>
    <td><input name='email' type='text' size='$text_size' value='$email'><font  color='#000099'> Hopefully your email is correct!!!</font></td>
  </tr>
</table></td></tr>
     <tr><td colspan='3'><br><strong><li><font  color='#FF0000'>*</font><u>Examinations Centre</u></li></strong> [Where would you like to sit for your $d5_diet exams?]</td></tr>
      <tr><td width='30%'>";
	  if($center=='Arua'){
	  	echo "<input name='center' type='radio' id='Arua' value='Arua' checked='checked'>Arua<br>";
	  }else{
	  	echo "<input name='center' type='radio' id='Arua' value='Arua'>Arua<br>";
	  }
	  if($center=='Fort Portal'){
	  	echo "<input name='center' type='radio' id='Fort Portal' value='Fort Portal' checked='checked'>Fort Portal";
	  }else{
	  	echo "<input name='center' type='radio' id='Fort Portal' value='Fort Portal'>Fort Portal";
	  }
	  
    	echo "</td>
    	<td  width='30%'>";
		
			  if($center=='Gulu'){
	  	echo "<input name='center' type='radio' id='Gulu' value='Gulu' checked='checked'>Gulu<br>";
	  }else{
	  	echo "<input name='center' type='radio' id='Gulu' value='Gulu'>Gulu<br>";
	  }
	  if($center=='Kampala'){
	  	echo "<input name='center' type='radio' id='Kampala' value='Kampala' checked='checked'>Kampala";
	  }else{
	  	echo "<input name='center' type='radio' id='Kampala' value='Kampala'>Kampala";
	  }

    	echo '</td>
    	<td>';

		
			  if($center=='Mbale'){
	  	echo "<input name='center' type='radio' id='Mbale' value='Mbale' checked='checked'>Mbale<br>";
	  }else{
	  	echo "<input name='center' type='radio' id='Mbale' value='Mbale'>Mbale<br>";
	  }
	  if($center=='Mbarara'){
	  	echo "<input name='center' type='radio' id='Mbarara' value='Mbarara' checked='checked'>Mbarara";
	  }else{
	  	echo "<input name='center' type='radio' id='Mbarara' value='Mbarara'>Mbarara";
	  }


    
     
         echo "</td>
         </tr>
         <tr>
            	<td colspan='3'><br> <strong><li><font  color='#FF0000'>*</font><u>Examinations Entry</u></li></strong> </td>
            </tr>
            <tr>
            <td colspan='3'>
<table border='1' cellspacing='0' cellpadding='2'>
  <tr>
    <th scope='col'>Paper</th>
    <th scope='col'>Subject </th>
    <th scope='col'>Cost (UGX)</th>
    <th scope='col'>Tick </th>
    <th scope='col'>College <font size='2'>[Select 'Private' if not applicable]</font></th>
  </tr>";

			if($confirmed==1){
     			$displayed_paper_array = explode(',',$final_reg);
			}else{
	     		$displayed_paper_array = explode(',',$all_eligibility);
			}


	for($i=0; $i < count($displayed_paper_array); $i++){
		$sql = mysql_query("SELECT name,cost FROM j310_icpau_papers WHERE course='$course' AND paper = $displayed_paper_array[$i]");
		if(mysql_num_rows($sql)>0){
			while($row = mysql_fetch_array($sql)){
				$cost =$row['cost'];
				$cost_string = substr_replace($cost,',',-3,0);
				$name = $row['name'];
				$paper_val = $displayed_paper_array[$i];
				$trinst_code = $ticked_college_codes_array[$i];
				$trinst_name = $ticked_college_names_array[$i];
	
			echo "
            <tr>
				<td>$displayed_paper_array[$i]</td>
    			<td>$name</td>
    			<td align='right'>$cost_string</td>";


			if($confirmed==1){
				echo "<td><input name='ticked_papers[]' type='checkbox' value='$paper_val' checked/></td>";
			}else{
				echo "<td><input name='ticked_papers[]' type='checkbox' value='$paper_val' /></td>";
			}
				
				echo "
                <td><select name='college[]'>
                <option value=''>Training Institution ...</option>";

			if($confirmed==1){
				echo "
				<option value= '$trinst_code' selected> $trinst_name </option>";
			}
				
				echo "
				$college_list
				
                </select> 
                </td>
                </tr>";
				} // end while
			} // end if
		} // end for
   
   
echo "</table>
            </td>
            </tr>";
			if($confirmed==0){
			        echo "<tr>
            	<td colspan='3'><br> <strong><li><font  color='#FF0000'>*</font><u>Payment Details</u></li></strong> [The payment details will enable the ICPAU Finance Department to easily trace your examinations bank transaction.]</td>
            </tr>

			<td colspan='3'>
		<table width='91%' border='0' cellspacing='0' cellpadding='2'>
  <tr>
    <td width='30%'>Date of Payment <font size='2'>(dd-mm-yyyy)</font></td>
    <td colspan='6'>
	    <input name='date_of_payment' id='date_of_payment' type='text' size='25'>";
?><a href="javascript:NewCal('date_of_payment','ddmmmyyyy')"><img src="scripts/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
 <?php  
   echo "</td>
  </tr>
  <tr>
    <td>Amount Paid &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; UGX</td>
    <td colspan='6'>
      <input type='text' name='amount_paid' size='25' id='amount_paid' />/=  (Exclusive of bank charges)
    </td>
  </tr>
  <tr>
    <td>Tick Items Paid For  =====></td>
    <td width='2%'>
      <input type='checkbox' name='examination' id='examination' value='Examination' />
    </td>
    <td width='17%'>Examination</td>
    <td width='2%'>
      <input type='checkbox' name='annual_renewal' id='annual_renewal' value='Annual Renewal'/>
    </td>
    <td width='20%'>Annual Renewal</td>
    <td width='2%'>
      <input type='checkbox' name='exemptions' id='exemptions' value='Exemptions' />
    </td>
    <td width='29%'>Exemptions</td>
  </tr>
</table>";}
echo "
			</td>
			</tr>
            <tr>
            	<td colspan='3'><br> <strong><li><font  color='#FF0000'>*</font><u>Declaration</u></li></strong> </td>
            </tr>
      <tr>
       <td colspan='3'><input name='declaration' type='checkbox' id='declaration' value='declaration' /> I have read and understood the progression rules and hereby declare that the information given above is correct. 
       </td>
      </tr>
      <tr>
       <td colspan='2'><br><center><input name='submit' type='submit' value='Click here to submit your form.' /></center> 
       </td>
      </tr>
      
            </table>
    </form></ol>";
	
			 }// end else for comfirmed member
	}
	//====== end of form function







//banner();

//====================================== Processing of candidate BEGINS

 if($_POST['submit']){
	   		$co =trim($_POST['co']);
			$co = str_replace("'","`",$co);
			$box =trim($_POST['box']);
			$box = str_replace("'","`",$box);
	   		$confirmed =trim($_POST['confirmed']);
	   		$country =trim($_POST['country']);
	   		$d5_diet=$_POST['d5_diet'];
	   		$d3_diet=$_POST['d3_diet'];
			$country = str_replace("'","`",$country);
			$town =trim($_POST['town']);
			$town = str_replace("'","`",$town);
			$mobile=trim($_POST['mobile']);
			$mobile = str_replace("'","`",$mobile);
			$email=trim($_POST['email']);
			$declaration=$_POST['declaration'];
			$email = str_replace("'","`",$email);
			$center=trim($_POST['center']);
			$low_eligibility=$_POST['low_eligibility'];
			$all_eligibility=$_POST['all_eligibility'];
			$high_eligibility =$_POST['high_eligibility'];
			$all_papers=$_POST['all_papers'];
			$college=$_POST['college'];
			$initial_date=$_POST['initial_date'];
			$course=$_POST['course'];
			$_user=$_POST['_user'];
			$initial_reg = $_POST['initial_reg'];
			$final_reg = $_POST['final_reg'];
			$ticked_papers = $_POST['ticked_papers'];
			$date_of_payment =$_POST['date_of_payment'];
			$date_of_payment = date('Y-m-d',strtotime($date_of_payment));
			$amount_paid =$_POST['amount_paid'];
			$amount_paid = str_replace(',','',$amount_paid);
			$annual_renewal =$_POST['annual_renewal'];
			$exemptions =$_POST['exemptions'];
			$examination =$_POST['examination'];
			
			
			
			if(!empty($examination)){
					$item_paid = $examination.",";
				}
			if(!empty($exemptions)){
					$item_paid .= $exemptions.",";
				}
			if(!empty($annual_renewal)){
					$item_paid .= $annual_renewal.",";
				}
			
			$item_paid =trim($item_paid,',');
			
	//chk1- ensure all mandatory fields are filled
		if(empty($box)|| empty($country) || empty($town) || empty($mobile) || empty($email)
|| empty($center)|| empty($declaration) ){
		$_SESSION['error']="ERROR:Ensure all mandatory fields are filled";
		  registration_form($_user,$d5_diet,$d3_diet);
footer($d5_diet);
		exit;
	}

	if($confirmed==0 && empty($date_of_payment) && empty($amount_paid ) && (empty($annual_renewal) || empty($exemptions )||empty($examination))){
$_SESSION['error']="ERROR:Ensure your payment details are filled";
		  registration_form($_user,$d5_diet,$d3_diet);
footer($d5_diet);
		exit;
}	

	if($confirmed==0){
			$display_paper_array= explode(',',$all_eligibility);
			}else{
				$display_paper_array = explode(',',$final_reg);
				}
		$college_string = '';
		for($i=0;$i<count($ticked_papers);$i++){
				$key = implode(array_keys($display_paper_array,$ticked_papers[$i]));
				if($college[$key]==''){
					$_SESSION['error']="ERROR: Ensure that ticked papers have corresponding colleges selected";
		  				registration_form($_user,$d5_diet,$d3_diet);
footer($d5_diet);
				 exit;	
					}
					$college_string .=',' .$college[$key];
			}// end of for loop
			
			$college_string= trim($college_string,',');
			
			
			$low_eligibility_array= explode(',',$low_eligibility);
			$high_eligibility_array= explode(',',$high_eligibility);			
			$ticked_papers_string =implode(',',$ticked_papers);
			//$college_string=implode(',',$college);
			
			
			
			//chk1- count registered == 0
			if(empty($ticked_papers) and $confirmed==0){
				 $_SESSION['error']="Ensure at least 1 paper is filled";
		  				registration_form($_user,$d5_diet,$d3_diet);
footer($d5_diet);
				 exit;
				}
				
			//chk2- 
			if($all_papers==1 && $low_eligibility !=$ticked_papers_string && !empty($ticked_papers_string)){
                           if ($confirmed == 1){
				$_SESSION['error']="ERROR: To withdraw - ALL papers (".$low_eligibility.") must be unchecked";
                           }else{
				$_SESSION['error']="ERROR: Ensure papers (".$low_eligibility.") are ALL checked";
                           }
		  				registration_form($_user,$d5_diet,$d3_diet);
footer($d5_diet);
				 exit;
				}
			
			//chk3-
			if(array_intersect($high_eligibility_array,$ticked_papers)){
					if(!strstr($ticked_papers_string,$low_eligibility)){
                           if ($confirmed == 1){
				$_SESSION['error']="ERROR: To withdraw from lower level papers (".$low_eligibility.") - ensure that all higher level papers are unchecked";
                           }else{
						$_SESSION['error']="ERROR: Ticking higher level paper requires that you should include all lower level papers (".$low_eligibility.")";
                           }
		  				registration_form($_user,$d5_diet,$d3_diet);
footer($d5_diet);
						exit;
						}
				}
				
			//chk 4-
			if(count($ticked_papers)>4 && $course=='ATC'){
				 $_SESSION['error']="ERROR: You ought to tick at most 4 papers";
		  				registration_form($_user,$d5_diet,$d3_diet);
footer($d5_diet);
						exit;
				}
			
		
			//=========================================== version 3.0 =======================================
			// Fresh registration	
		
			if (empty($initial_reg) and !empty($ticked_papers)){
				$query = mysql_query("UPDATE j310_registration SET initial_reg='$ticked_papers_string', final_reg='$ticked_papers_string', initial_date=now(), final_date=now(),colleges='$college_string', center='$center',initial_modified=1, date_of_payment='$date_of_payment',
				amount='$amount_paid', item_paid='$item_paid',download=1 WHERE  regno='$_user'");
				
				$query = mysql_query("UPDATE j310_address SET co='$co', box ='$box', country='$country', town='$town', mobile='$mobile', email='$email' WHERE regno='$_user'");

				$query = mysql_query("UPDATE zeby_users SET email='$email' WHERE username='$_user'");
$address = "$co,<br>$box";
				$query = mysql_query("UPDATE zeby_students SET address ='$address', country='$country', city='$town', phone='$mobile' WHERE username='$_user'");
//echo "$box $email";
//echo "UPDATE zeby_students SET address ='$address', country='$country', city='$town', phone='$mobile' WHERE username='$_user'";
			}
			
			
			// withrawing person
			if($confirmed==1){
			
			if($ticked_papers!=$display_paper_array){
				$query=mysql_query("UPDATE j310_registration SET  final_reg='$ticked_papers_string', final_date= now(),center='$center', final_modification=1,colleges='$college_string',withdrew=1,download=1 WHERE  regno='$_user'");
				}else{
				$query=mysql_query("UPDATE j310_registration SET  final_reg='$ticked_papers_string', final_date= now(),center='$center', final_modification=1,colleges='$college_string',withdrew=0,download=1 WHERE  regno='$_user'");
				}
				
				$query = mysql_query("UPDATE j310_address SET co='$co', box ='$box', country='$country', town='$town', mobile='$mobile', email='$email' WHERE regno='$_user'");


				$query = mysql_query("UPDATE zeby_users SET email='$email' WHERE username='$_user'");
$address = "$co,<br>$box";
				$query = mysql_query("UPDATE zeby_students SET address ='$address', country='$country', city='$town', phone='$mobile' WHERE username='$_user'");
			}
			//=========================================== version 3.0 =======================================
		 
				
} //=== Processing of candidate ENDS

registration_form($_user,$d5_diet,$d3_diet);

footer($d5_diet);
?>