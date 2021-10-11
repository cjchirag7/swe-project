<?php
	$ui = new UI();
	
	function getOptionFromText($row,$val_name, $text_name, $selectedValue)
	{
	    $ui = new UI();
	    $isSelected = ($row->$val_name)===$selectedValue;
	    return $ui->option()
	              ->value($row->$val_name)
	              ->text($row->$text_name)
	              ->selected($isSelected);
	}
	
	function getAllOption($isSelected)
	{
	    $ui = new UI();
	    return $ui->option()
	    ->value('All')
	    ->text('Select')
	    ->selected($isSelected);
	}
		
	if(!isset($session))
	      $session='';
	if(!isset($session_year))
	          $session_year='';
	if(!isset($branch))
	          $branch='';
    if(!isset($course))
	          $course='';
    if(!isset($semester))
	          $semester='';
    if(!isset($department))
	          $department='';
	
	$sessionOptions = array_map(function ($item) use ($session){ 
	    return getOptionFromText($item,'session','session',$session);
	},$sessions);
	
	array_unshift($sessionOptions,getAllOption($session==''));
	   
	$sessionYearOptions = array_map(function ($item) use ($session_year){
	    return getOptionFromText($item,'session_year','session_year',$session_year);
	},$sessionYears);
	
	array_unshift($sessionYearOptions ,getAllOption($session_year==''));
	
	$departmentOptions = array_map(function ($item) use ($department){
	    return getOptionFromText($item,'id','name',$department);
	},$departments);

	array_unshift($departmentOptions,getAllOption($department==''));
	
	$courseOptions = array_map(function ($item) use ($course){
	    return getOptionFromText($item,'id','name',$course);
	},$courses);
	
	array_unshift($courseOptions,getAllOption($course==''));
	
	$branchOptions = array_map(function ($item) use ($branch){
	    return getOptionFromText($item,'id','name',$branch);
	},$branches);
	
	array_unshift($branchOptions,getAllOption($branch==''));
	
	$semesterOptions = array_map(function ($item) use ($semester){
	    return getOptionFromText($item,'semester','semester',$semester);
	},$semesters);
	
	array_unshift($semesterOptions,getAllOption(semester==''));
	
	$box = $ui->box()
			  ->width(12)
			  ->open();
		$form = $ui->form()
		   		   ->multipart()
		   		   ->action('physical_registration_monitor')
		   		   ->open();

		   		$ui->select()
		   		   ->width(6)
		   		   ->name('session_year')
		   		   ->label('Session year')
		   		   ->options($sessionYearOptions)
		   		   ->required()
		   		   ->show();
		   		
		   		$ui->select()
		   		   ->width(6)
		   		   ->name('session')
		   		   ->label('Session')
		   		   ->options($sessionOptions)
		   		   ->required()
		   		   ->show();
		   		
		   		$ui->select()
		   		   ->width(6)
		   		   ->name('department')
		   		   ->label('Department')
		   		   ->options($departmentOptions)
		   		   ->required()
		   		   ->show();
		   		   
		   		$ui->select()
		   		   ->width(6)
		   		   ->name('course')
		   		   ->label('Course')
		   		   ->options($courseOptions)
		   		   ->required()
		   		   ->show();
		   		
		   		$ui->select()
		   		   ->width(6)
		   		   ->name('branch')
		   		   ->label('Branch')
		   		   ->options($branchOptions)
		   		   ->required()
		   		   ->show();
		   		   
		   		$ui->select()
		   		   ->width(6)
		   		   ->name('semester')
		   		   ->label('Semester')
		   		   ->options($semesterOptions)
		   		   ->required()
		   		   ->show();

			echo '<center>';
				$ui->button()
				   ->value('Submit')
				   ->uiType('primary')
				   ->id('submit')
				   ->submit()
				   ->show();
			echo '</center>';
		$form->close();
	$box->close();	
	echo '<br/>';
if(isset($submitted))
{
    $totalCount=0;
    foreach ($summary as $row){
        $totalCount+=$row['count'];
    }
    if(isset($no_select))
    {
        echo '<div>';
        $ui->callout()
        ->uiType('error')
        ->title('No option selected')
        ->desc('Please select any of the above fields and try again!')
        ->dismiss(true)
        ->show();
        echo '</div>';
    }
    else if ($totalCount === 0)
    {
        echo '<div>';
        $ui->callout()
           ->uiType('error')
           ->title('No records match your query')
           ->desc('Please recheck the selected fields above and try again!')
           ->dismiss(true)
           ->show();
           echo '</div>';
    }
    else 
    {    
    $resultsTabBox = $ui->tabBox()
        ->tab("summary", $ui->icon("pie-chart") . " Summary ", true)
        ->tab("details", $ui->icon("table") . " Details")
        ->open();

    $summaryTab = $ui->tabPane()
        ->id("summary")
        ->active()
        ->open();    
    echo '<div id="summary"> <div id="summary-print"> <div id="summaryChartContainer" style="height: 300px; width: 100%;"></div>';
    echo '<h5 align="center"> Total students : '.$totalCount.'</h5><br/>';
    echo '</div>';
    echo '<center>';
    $ui->button()
    ->uiType('primary')
    ->id('printSummary')
    ->icon(new Icon("print"))
    ->value("Print")
    ->extras('onclick="printSummary()"')
    ->show();
    echo '</center>';
    echo '</div>';
    $summaryTab->close();
    $detailsTab = $ui->tabPane()
        ->id("details")
        ->open();
    echo '<div id="details">' ;
    echo '<div id="details-print">' ;
    echo '<table id="detailsTable" class="table table-hover table-bordered dataTable">';
    ?>
						<thead>
							<tr>
								<th>Admission No.</th>
								<th>Branch</th>
								<th>Course</th>
								<th>Department</th>								
								<th><center>Semester</center></th>
								<th><center>Fee paid?</center></th>								
								<th><center>Physical Registration Status?</center></th>
							</tr>
						</thead>
<?php
                    $admn_no='admn_no';
                    $branch='branch';
                    $course='course';
                    $semester='semester';
                    $department='department';
                    $fee_status='fee_status';
                    $physical_registration_status='physical_registration_status';
					foreach($details as $record)
					{
						echo '<tr>';
									echo '<td>'.$record->$admn_no.'</td>';
									echo '<td>'.$record->$branch.'</td>';
									echo '<td>'.$record->$course.'</td>';
									echo '<td>'.$record->$department.'</td>';
									echo '<td><center>'.$record->$semester.'</center></td>';
									echo '<td><center>'.($record->$fee_status?'<i class="fa fa-check-square-o"/>':'<i class="fa fa-square-o"/>').'</center></td>';
									if($record->$physical_registration_status)
    									echo '<td><center><i class="fa fa-check-square-o"/></center></td>';
    								else if($record->$fee_status)
    								    echo '<td><center><a href="'.base_url().'index.php/student_sem_form_all/viewandprint_all/pre_reg_admn_details">Register</a></center></td>';
    								else 
    								    echo '<td><center><i class="fa fa-square-o"/></center></td>';
						echo '</tr>';
					}
		echo '</table>';
		echo '</div>';
		echo '<center>';
        $ui->printButton()
        ->uiType('primary')
        ->id('printDetails')
        ->show();
    echo '</div>';
    $detailsTab->close();
    $resultsTabBox->close(); 
    }
}
?>
<?php echo '<script type="text/javascript" src="'.base_url().'/assets/js/physical_registration_monitor/chart_loader.js"></script>'?>
<script type="text/javascript">
			google.charts.load('visualization', "1", {
				packages: ['corechart']
			});

			google.charts.setOnLoadCallback(drawBarChart);
			
			function drawBarChart() {
				var data = google.visualization.arrayToDataTable([
					['Category', 'Count'], 
						<?php 
						foreach ($summary as $row){
							   echo "['".$row['category']."',".$row['count']."],";
						  }
						?>
				]);
				var options = {
					title: ' ',
					is3D: false,
				};
				var chart = new google.visualization.PieChart(document.getElementById('summaryChartContainer'));
				chart.draw(data, options);
			}
			
			function printSummary(){
			var divToPrint=document.getElementById('summary-print');
			var newWin=window.open('','Print-Window');
			newWin.document.open();
			newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
			newWin.document.close();
			setTimeout(function(){newWin.close();},10);					
			}
			
	$(document).ready(function(){
		$('.dataTable').dataTable({
			"searchable":true,
    		"paginated":true,
		    "aaSorting": [[6, 'desc'],[0,'asc']]
        });
        $("html").attr('style', function(i,s) { return (s || '') + 'max-height: 1000px !important;' });
	});			
</script>
<style>
@media print {
  a[href]:after {
    content: none !important;
  }
}
</style>