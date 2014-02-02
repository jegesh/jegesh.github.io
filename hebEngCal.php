<?php
class HeEnEventCal{
	public function __construct($tableName="",$dateField="",$linkIdField="",$dateFormat="",$linkURL="",$dbArray=Array(),$days=60, $hebTitles=TRUE,$eventText=FALSE,$eventTextField=""){
//	protected $dbArray = $dbArray;
	public $this->tableName = $tableName;
	public $this->dateField = $dateField;
	public $this->dateFormat = $dateFormat;
	public $this->linkURL = $linkURL;
	public $this->days = $days;
	public $this->eventText=$eventText;
	public $this->eventTextField=$eventTextField;
	public $this->hebTitles = $hebTitles;
	public $this->linkIdField = $linkIdField;
	protected @$this->PDOStr="'mysql:host=$dbArray[0];dbname=$dbArray[1]','$dbArray[2]' ,'$dbArray[3]'" ;;
	}
	public function setDbParams($dbArray){
		if($dbArray)
		protected $host = $dbArray[0];
		protected $dbName = $dbArray[1];
		protected $dbUser = $dbArray[2];
		protected $dbPw = $dbArray[3];
		$PDOStr = "'mysql:host=$host;dbname=$dbName','$dbUser' ,'$dbPw'" ;
		
	}
	protected $enHebMonths = array("תשרי"=>"Tishrei","חשון"=>"Cheshvan","כסלו"=>"Kislev","טבת"=>"Tevet","שבט"=>"Shevat","אדר"=>"Adar","אדר א'"=>"Adar I",
	"אדר ב'"=>"Adar II","ניסן"=>"Nissan","אייר"=>"Iyar","סיון"=>"Sivan","תמוז"=>"Tammuz","אב"=>"Av","אלול"=>"Elul"); // need to check spellings of months in php hebrew dates functions
	protected $hebGregMonths = array("January"=>"ינואר","February"=>"פברואר","March"=>"מרץ","April"=>"אפריל","May"=>"מאי","June"=>"יוני",
	"July"=>"יולי","August"=>"אוגוסט","September"=>"ספטמבר","October"=>"אוקטובר","November"=>"נובמבר","December"=>"דצמבר" );
	
	public function printCal(){
		echo '<table id="hebEngCal" border="2" cellpadding="3px" ><caption><b>';
		if($this->hebTitles){
		echo 'אירועים קרובים';
		}else{
			echo 'Upcoming Events';
		}
		echo '</b><br />';	
		$wDay = date('N');	
		setlocale(LC_TIME, "heb_heb"); // is this necessary? does it do anything?
		$formattedDate = new DateTime();
		/* get month range and display it in calendar <caption> tag *** still  need to fix adar I, II quirk *** */
		$hebMonth = new DateTime();
		$gregDate = gregoriantojd($hebMonth->format('n'),$hebMonth->format('j'),$hebMonth->format('Y'));
		$jewDate = jdtojewish($gregDate,TRUE,CAL_JEWISH_ADD_GERESHAYIM);
		$jewdStr = iconv ('WINDOWS-1255', 'UTF-8', $jewDate);
		$jewDay1 = explode(" ", $jewdStr);
		$hebMonth->add(new DateInterval("P".$days."D"));
		$gregDate = gregoriantojd($hebMonth->format('n'),$hebMonth->format('j'),$hebMonth->format('Y'));
		$jewDate = jdtojewish($gregDate,TRUE,CAL_JEWISH_ADD_GERESHAYIM);
		$jewdStr = iconv ('WINDOWS-1255', 'UTF-8', $jewDate);
		$jewDay2 = explode(" ", $jewdStr);
		if($this->hebTitles){
			echo $jewDay1[1]." - ".$jewDay2[1]."<br />"; // print Hebrew months in Hebrew
		}else{
			echo $this->enHebMonths[$jewDay1[1]]." - ".$this->enHebMonths[$jewDay2[1]]."<br />"; // transliterate names of Hebrew months
		}
		$engMonth1 = $formattedDate->format('F'); // month
		$range = 1;
		if((date('j')+intval($this->days))>30){ // determine how many months the calendar spans
			$range = ceil($this->days/30);
		}
		$formattedDate->add(new DateInterval("P".$range."M"));
		$engMonth2 = $formattedDate->format('F');
		if($this->hebTitles){
			echo $this->hebGregMonths[$engMonth1]." - ".$this->hebGregMonths[$engMonth2];  // print Gregorian months in Hebrew
		}else{
		echo $engMonth1." - ".$engMonth2; // Gregorian months in plain English
		}
		if($this->hebTitles){
			echo "</caption><tr><th>א'</th><th>ב'</th><th>ג'</th><th>ד'</th><th>ה'</th><th>ו'</th><th>ש'</th></tr><tr>";
		}else{
			echo "</caption><tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr><tr>";
		}
		for($i=1;$i<$wDay;$i++){
			echo "<td></td>";
		}
		$bgClass = 1; // determines background color of month
		$calDate = new DateTime();
		$dbh = new PDO("mysql:host=$this->PDOStr[0];dbname=$this->PDOStr[1]", "$this->PDOStr[2]", "$this->PDOStr[3]");
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
		if($this->eventText){
			$statement=$dbh->prepare("SELECT $this->linkIdField, $this->eventTextField FROM $this->tableName WHERE $dateField = ?");
		}else{
			$statement=$dbh->prepare("SELECT $this->linkIdField FROM $this->tableName WHERE $dateField = ?");
		}
		for($i=0;$i<$this->days;$i++){
			$calDate->add(new DateInterval('P1D'));	
			if($calDate->format('j')==1){ // need to find a solution to change class at beginning of Hebrew month instead
				$bgClass++;
			}
			$tdClass = 'color'.strval($bgClass);
			echo "<td class='$tdClass'><div class='event'>";
			echo '<span class="secDate">';
			echo $calDate->format('j');
			echo "</span>";
			echo "<span class='jDate'>";
			$gDate = gregoriantojd($calDate->format('n'),$calDate->format('j'),$calDate->format('Y'));
			$jDate = jdtojewish($gDate,TRUE,CAL_JEWISH_ADD_GERESHAYIM);
			$jdStr = iconv ('WINDOWS-1255', 'UTF-8', $jDate);
			$jDay = explode(" ", $jdStr);
			echo $jDay[0]."</span>";
			// content for date
			$queryDate = $calDate->format($this->dateFormat);
			$statement->bindParam(1,$queryDate);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_BOTH);
			if (!empty($result)) { // if event exists for given date
				if($this->eventText){
					echo "<a href='/pages/workshops.php?id=$result[0]' class='cal_link'><div style='width:100%;height:100%'>$result[1]</div></a> ";
				}else{
					echo "<a href='/pages/workshops.php?id=$result[0]' class='cal_link'><div style='width:100%;height:100%'>&nbsp;</div></a> ";
				}
			}
			echo "</div></td>";
			if(($i+$wDay) % 7 == 0){
				echo "</tr><tr>";
			}
			if($i==$days-1){
				for($j=1;$j<12-$wDay;$j++){
					echo "<td class='$tdClass'></td>";
				}
			}
		}
		echo "</tr></table>";
	}
}

?>
