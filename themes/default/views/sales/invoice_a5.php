<!doctype>
<html>
	<head>
		<title>Invoice a5</title>
		<meta charset="utf-8">
		<style>
			@media print{
			
				#tb tr th{
					background-color: #DCDCDC !important;
				}
				#No{
					width:10px;
				}
				#code{
					width:30px;
				}
				 
				#unit{
					width:100px;
				}
				#box-content{
					page-break-inside:auto;
				}
				#qty{
					width:150px;
				}
				#unit_price{
					width:150px;
				}
				#amount{
					width:200px;
				}
				#add tr td p{
					font-size:14px !important;
				}
				p{
					font-size:11px !important;
				}
				#print{
					display:none;
				}
				#foot{
					width:100%;
					background:#fff !important;
				}	
				.fon{
					color: rgba(0, 0, 0, 0.3) !important;
				}
				.left_ch{
					 left: 80px !important;
				}
				#tb{
					margin-bottom:0px !important;
				}
				 
			}
			#print{
				
				width:60px;
				height:45px;
				border:0px;
				background: #4169E1;
				color:#fff;
				cursor:pointer;
				-webkit-box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.75);
box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.75);
			}
			#body,h2,h3,h4,h5,p{
				margin:0px;
				padding:5px;
				
			}
			
			#body{
				width:95%;
				height:100%;
				margin:0 auto;
			 
			}		

		
			#top{
				width:95%;
				height:100px;
				margin:0 auto;
				
				padding-top:20px;
			}
			#top_l{
				width:220px;
				float:left;
			}
			#top_r{
				width:200px;
				float:right;
				text-align:center;
			}
			h1,h2,h3,h4{
				font-family:"Khmer OS Muol";
				padding:0px;
			}
			
			p{
				font-size:15px;
				font-family:"Arial Narrow";
			}
			#top2{
				width:95%;
				margin:0 auto;
				text-align:center;
				height:170px;
				
				margin-bottom:10px;
			}
			#top2_l{
				width:30%;
				margin:0 auto;
				text-align:left;
				
				float:left;
				height:150px;
			}
			#top2_c{
				width:30%;
				margin:0 auto;
				text-align:center;
				height:150px;
				
				float:left;
			}
			#top2_r{
				width:30%;
				margin:0 auto;
				text-align:left;
				
				float:left;
				height:210px;
			}
			#top2 h5{
				font-family:"Khmer OS Muol";
			}
			#tb tr th{
				font-size:14px;
				padding:5px;
				font-family:"Arial Narrow";
				 font-weight: bold;
			}
			
			#tb tr td{
					font-size:14px; 
					font-family:"Arial Narrow";
				}
				#tb {
					width:95%;
					margin:25px auto;
					margin-bottom:0px;
				}
				#foot{
				width:100%;
				height:150px;
				background:#F0F8FF;	
			}		
			#tb2 tr td,#tb3 tr td{
				font-size:15px;
				font-family:"Arial Narrow";
				text-align:left;
				padding-left:10px;
			}
			.row{
				float: left;
				padding-left: 32px;
				padding-right: 10px;
				width: 94%;
			}
			.footer-left{
				width:60%;
				float:left;
			}
			.footer-right{
			    width:39%;
				float:left;	 
			}
			.border{
				border-left:1px solid black !important;
				border-right:1px solid black !important;	
				border-bottom:0px !important;	
				border-top:0px !important;	
			}
			.border_top{
				border-top:1px solid black !important;	
				border-bottom:1px solid black !important;	
			}
			.inv_footer{
				border-top: 1px solid black !important;
				border-right:1px solid black !important;
			}
			.border_bottom{
				border-bottom:1px solid black !important;
				border-top:1px solid black !important;
				border-left:1px solid black !important;
				border-right:1px solid black !important;
			}
			.left{
				padding-right: 3px!important;
			}
			.right{
				padding-left: 4px !important;
			}
		</style>
	</head>
	<body>
	 
		 <button id="print" onclick="window.print()">
				<img src="<?= base_url() . 'assets/uploads/printer.png'; ?>">
		 </button> 
		 <div id="top2"> 
			
			<h1><b><?=lang("Tea_try_II")?></b></h1>
			
			<br>
			<div style="float:left; width:50%;border: 1px solid #000000;">
				<table id="add" style="width:100%;padding-left:6px">
					<tr>
						<td width="17%"><p style="font-size:14px;"><b><?=lang("customer")?></b></p></td>
						<td>: <?=$customer->company;?></td>
					</tr>
					<tr>
						<td><p style="font-size:14px;"><b><?=lang("address")?></b></p></td>
						<td>: <?=$customer->address;?></td>
					</tr>
					<tr>
						<td><p style="font-size:14px;"><b><?=lang("Tel/Fax")?></b></p></td>
						<td>: <?=$customer->phone;?></td>
					</tr>
					 
				</table>
			</div>
			<div id="add" style="float:right; width:40%;border: 1px solid #000000;">
				<table id="tb3" style=" width:100%;">
					<tr>
						<td width="28%"><p style="font-size:14px;"><b><?=lang("date")?></b></p></td>
						<td>: <?=$this->erp->hrsd($inv->date);?></td>
					</tr>
					<tr>
						<td><p style="font-size:14px;"><b><?=lang("invoice_no")?></b></p></td>
						<td>: <?=$inv->reference_no?></td>
					</tr> 
				</table>
			</div>
			
			</div> 
	<tr>
		 <td>
			<table id="tb" style="border-collapse: collapse;" border="1" >
				<thead>
				<tr style="background-color:#E9EBEC">
					<th id="No"style="text-align:center;width:10px">ល.រ</br>Nº</th>
					<th id="code"style="text-align:center;width:30px">លេខកូដទំនិញ<br>Product code</th>
					<th id="description"style="text-align:center;width:200px;">បិរយាយ<br>Description</th>
					<th id="unit"style="text-align:center;width:70px">ខ្នាត<br>Unit</th>
					<?php 
					    if ($Settings->product_discount) {
					 echo '<th id="discount"style="text-align:center;width:70px">បញ្ចុះតំលៃ<br>Discount</th>';
						}
					?>
					<th id="qty"style="text-align:center;width:100px">ចំនួន<br>Qty</th>
					<th id="unit_price"style="text-align:center;width:100px;">តំលៃ<br>Unit Price</th>
					<th id="amount"style="text-align:center;width:100px">សរុប<br>Amount</th>
					
				</tr>
				</thead>
				</tbody>
				<?php  
						 
						$r=1;
                    foreach($rows as $row){						
						?>
				<tr>
					<td valign="top" class="border" style="text-align:center; width:40px; "><?= $r; ?></td>
											
					<td class="border right">
						 <?=$row->product_code?>
					</td>
					
					<td class="border" style="width: 80px; text-align:left;padding-left:5px;"><?=$row->product_name?></td>
					<td class="border left" style="text-align:center; width:70px;"><?=$row->uname?></td>
					<?php					
					if ($Settings->product_discount){
						echo '<td class="border" style="width: 100px; text-align:right; vertical-align:middle;">' . ($rows[$i]->discount != 0 ? '<small>(' . $rows[$i]->discount . ')</small> ' : '') . $this->erp->formatMoney($row->item_discount) . '</td>';
					}
					?>
					<td class="border" style="width: 80px; text-align:center;"><?= $this->erp->formatQuantity($row->quantity); ?></td>
					<td class="border left" style="text-align:center; width:100px;"><?=$this->erp->formatMoney($row->unit_price); ?> </td> 
					<td class="border left" style="text-align:center; width:120px;"><?= $row->subtotal!=0?$this->erp->formatMoney($row->subtotal):$t; ?>&nbsp<?php echo $sym; ?> </td>
				</tr>
				<?php $total +=$row->subtotal; $r++; } 
				 if(count($rows) <=10){ 
				 
					for($i =0;$i <10; $i++){	
					$html ='<tr>
							<td valign="top" class="border" style="text-align:center; width:40px; ">&nbsp;</td>
							<td class="border right"></td>
							<td class="border right"></td>
							<td class="border left" style="text-align:right; width:70px;"></td>
							<td class="border" style="width: 80px; text-align:center;"></td>
							<td class="border left" style="text-align:right; width:100px;"></td> 
							<td class="border left" style="text-align:right; width:120px;"></td>
						</tr>';
						echo $html;
					}
					
					$i++;
				 } 
				  				 
				 if(count($rows) <=23){ 
				 
					for($i =0;$i <3; $i++){	
					$html ='<tr>
							<td valign="top" class="border" style="text-align:center; width:40px; ">&nbsp;</td>
							<td class="border right"></td>
							<td class="border right"></td>
							<td class="border left" style="text-align:right; width:70px;"></td>
							<td class="border" style="width: 80px; text-align:center;"></td>
							<td class="border left" style="text-align:right; width:100px;"></td> 
							<td class="border left" style="text-align:right; width:120px;"></td>
						</tr>';
						echo $html;
					}
					
					$i++;
				 } ?> 
				 
				</tbody>
				
			</table>
			 <?php if(count($rows) ==24 || count($rows) <=39 || count($rows) ==63 || count($rows) <=78) { ?>
			<table  id="tb" style="page-break-before:always;border-collapse: collapse;" border="1">
			      
                 <tfoot>
                 <tr valign="top">				 
			          
				      <td width="50%"> 
					   <h3>Additional Remark</h3>
					   <p>-ទំនិញទិញហើយមិនអាចប្តូរយកប្រាក់វិញបានឡើយ.</p>
					   <p>-អ្នកទិញត្រូវរាប់និងពិនិត្យទំនិញឲបានត្រឺមត្រូវ មុនចុះហត្ថលេខាទទួល.</p>
					   <p>-ទំនិញដែលប្រើសល់អាចសង់ចូលវិញបានតែក្នុងរយះពេល ៣០ថ្ងៃចាប់ ពីថ្ងៃទទួលទំនិញ.</p>
					   <p>-ចំពោះទំនិញដែលបានកម្មង់គឺមិនអាចសង់ចូលវិញបានទេ.</p>
					   <p>-តំលៃទំនិញទាំងអស់នេះប្រាក់ពន្ធបន្ថែម VAT 10%​ ជាបន្ទុករបស់នាក់ទិញ</p>
					   <div style="margin-top:62px"><p style="width:10%;border-top:dotted 1px black;margin:0 auto;"></p>
					   <p style="text-align:center">Customer Name And Signature</p>
					    <p style="">Date :.........../............/............/&nbsp;&nbsp;Phone:.........................................................</p>
					   </div>
					  </td> 
					  <td width="50%">
					  <table>
					         <tr>
							      <td width="20%"><h4><?= lang("grand_total") ?>&nbsp;&nbsp;&nbsp;&nbsp;:</h2></td>
								  <td style=""><p style="text-align:center;padding:0px;border-bottom:dotted 1px black;"><?=$this->erp->formatMoney($total)?>&nbsp;&nbsp; $</p></td>
							 </tr>
							 <tr>
							      <td><h4><?= lang("paid") ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</h4></td>
								  <td style=""><p style="text-align:center;padding:0px;border-bottom:dotted 1px black;"><?=$this->erp->formatMoney($inv->paid)?>&nbsp;&nbsp; $</p></td>
							 </tr>
							 <tr>
							      <td><h4><?= lang("balance") ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</h4></td>
								  <td style=""><p style="text-align:center;padding:0px;border-bottom:dotted 1px black;"><?=$this->erp->formatMoney($inv->grand_total-$inv->paid);?>&nbsp;&nbsp; $</td>
							 </tr>
							 <tr>
							      <td><h4><?= lang("amount_in_word") ?></h4></td>
								  <td></td>
							 </tr>
					  </table>
					  <p style="border-top:dotted 1px black;"></p>
					  <p style="border-bottom:dotted 1px black;margin-top:185px"></p>
					  <p>Authorized Signature <span style="padding-left:50px;">Seller</span></p>
					  </td>
				
				</tr>
				</tfoot> 
				</table>
				<?php }else{?>
				<table  id="tb" style="border-collapse: collapse;" border="1">
				<tfoot>
                 <tr valign="top">				 
			          
				      <td width="50%"> 
					   <h3>Additional Remark</h3>
					   <p>-ទំនិញទិញហើយមិនអាចប្តូរយកប្រាក់វិញបានឡើយ.</p>
					   <p>-អ្នកទិញត្រូវរាប់និងពិនិត្យទំនិញឲបានត្រឺមត្រូវ មុនចុះហត្ថលេខាទទួល.</p>
					   <p>-ទំនិញដែលប្រើសល់អាចសង់ចូលវិញបានតែក្នុងរយះពេល ៣០ថ្ងៃចាប់ ពីថ្ងៃទទួលទំនិញ.</p>
					   <p>-ចំពោះទំនិញដែលបានកម្មង់គឺមិនអាចសង់ចូលវិញបានទេ.</p>
					   <p>-តំលៃទំនិញទាំងអស់នេះប្រាក់ពន្ធបន្ថែម VAT 10%​ ជាបន្ទុករបស់នាក់ទិញ</p>
					   <div style="margin-top:62px"><p style="width:10%;border-top:dotted 1px black;margin:0 auto;"></p>
					   <p style="text-align:center">Customer Name And Signature</p>
					    <p style="">Date :.........../............/............/&nbsp;&nbsp;Phone:.........................................................</p>
					   </div>
					  </td> 
					  <td width="50%">
					  <table>
					         <tr>
							      <td width="20%"><h4><?= lang("grand_total") ?>&nbsp;&nbsp;&nbsp;&nbsp;:</h2></td>
								  <td style=""><p style="text-align:center;padding:0px;border-bottom:dotted 1px black;"><?=$this->erp->formatMoney($total)?>&nbsp;&nbsp; $</p></td>
							 </tr>
							 <tr>
							      <td><h4><?= lang("paid") ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</h4></td>
								  <td style=""><p style="text-align:center;padding:0px;border-bottom:dotted 1px black;"><?=$this->erp->formatMoney($inv->paid)?>&nbsp;&nbsp; $</p></td>
							 </tr>
							 <tr>
							      <td><h4><?= lang("balance") ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</h4></td>
								  <td style=""><p style="text-align:center;padding:0px;border-bottom:dotted 1px black;"><?=$this->erp->formatMoney($inv->grand_total-$inv->paid);?>&nbsp;&nbsp; $</td>
							 </tr>
							 <tr>
							      <td><h4><?= lang("amount_in_word") ?></h4></td>
								  <td></td>
							 </tr>
					  </table>
					  <p style="border-top:dotted 1px black;"></p>
					  <p style="border-bottom:dotted 1px black;margin-top:185px"></p>
					  <p>Authorized Signature <span style="padding-left:50px;">Seller</span></p>
					  </td>
				
				</tr>
				</tfoot>
					 
			</table>
			 <?php	} ?>
			   
	</body>
</html>