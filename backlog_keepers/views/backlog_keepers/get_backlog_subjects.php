<?php
	$ui = new UI();
		
	if(!isset($admn_no))
	    $admn_no='';
	
	$box = $ui->box()
			  ->width(12)
			  ->open();
	
		$form = $ui->form()
		   		   ->multipart()
		   		   ->action('backlog_keepers/get_backlog_subjects')
		   		   ->open();

		   		$ui->input()
		   		   ->width(12)
		   		   ->name('admn_no')
		   		   ->label('Admission Number')
		   		   ->required()
		   		   ->value($admn_no)
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
    if(isset($invalid_admn_no))
    {
        echo '<div>';
        $ui->callout()
        ->uiType('error')
        ->title('Invalid admission number')
        ->desc('There is no student with the above admission number. Please enter a valid admission number.')
        ->dismiss(true)
        ->show();
        echo '</div>';
    }
    else if (count($subjects) == 0)
    {
        echo '<div>';
        $ui->callout()
           ->uiType('error')
           ->title('Student doesn\'t have any backlog')
           ->desc('The student with this admission number doesn\'t have any backlog.')
           ->dismiss(true)
           ->show();
           echo '</div>';
    }
    else 
    {    
    echo '<div id="details">' ;
    echo '<div id="details-print">' ;
    
    echo '<table id="detailsTable" class="table table-hover table-bordered table-responsive">';
    ?>
						<thead>
							<tr>
								<th>S.No.</th>
								<th>Subject code</th>								
								<th>Subject</th>																								
								<th><center>Semester</center></th>								
								<th>Session</th>								
								<th>Session Year</th>																								
							</tr>
						</thead>
<?php
                    $ind=1;
                    $sub_code='sub_code';
                    $sub_name='sub_name';
                    $semester='semester';
                    $session='session';
                    $session_year='session_year';
					foreach($subjects as $record)
					{
						echo '<tr>';
            						echo '<td>'.$ind.'</td>';
            						echo '<td>'.$record->$sub_code.'</td>';
			             			echo '<td>'.$record->$sub_name.'</td>';
            						echo '<td><center>'.$record->$semester.'</center></td>';
									echo '<td>'.$record->$session.'</td>';
									echo '<td>'.$record->$session_year.'</td>';
						echo '</tr>';
						$ind++;
					}
		echo '</table>';
		echo '<br/>';
		echo '<center>';
		$ui->button()
		->uiType('primary')
		->id('details-print')
		->icon(new Icon("print"))
		->value("Print")
		->extras('onclick="printDetails()"')
		->show();
		echo '</center>';
		echo '</div>';
    echo '</div>';
    }
}
?>
<script type="text/javascript">
	/* Developed by dheeraj*/
			function printDetails(){
			$(this).hide();
			var divToPrint=document.getElementById('details-print');
			var newWin=window.open('','Print-Window');
			newWin.document.open();
			newWin.document.write('<html><head><style>td{cell-padding: 10px;} table, th, td {border: 1px solid black;border-collapse: collapse;}</style></head><body onload="window.print()"><h1>Backlog subjects of <?php echo $admn_no?></h1>'+divToPrint.innerHTML+'</body></html>');
			newWin.document.close();
			setTimeout(function(){newWin.close();},10);					
			$(this).show();
			}
</script>
<style>
@media print {
  a[href]:after {
    content: none !important;
  }
}
</style>
