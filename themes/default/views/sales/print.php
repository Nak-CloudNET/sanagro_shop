<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="invoce" content="width=device-width, initial-scale=1.0">
		<link href="<?= $assets ?>styles/helpers/bootstrap.min.css" rel="stylesheet"/>
		<title>Sanagro - Invoice</title>
		<style type="text/css">
			body {
				height: 100%;
				background: #FFF;
				font-size:12px;
			}
		
			th {
				text-align: center;
				font-family:"Arial Black";
				border: 1px solid #000;
				
			}
			td {
				border: 1px solid #000;
				padding-left:4px;
				padding-top:5px;
			}
			
			hr{
				border-color: #000;
				width:100px;
				margin-top: 70px;
			}
			p,h5,h4,h6{	
				font-family :"Arial";
			}
			.no_bd_b td{
				border-bottom:0px;
				border-top:0px;
				font-size:12px;
			}
			.no-bor td{
				border-top:0px;
				border-bottom:0px;
			}
			@media print
			{   
				body{
					-webkit-print-color-adjust: exact; 
					font-size:12px;
				}
				.sbody{
					font-size:15.5px !important;
				}
				#no_print
				{
					display: none !important;
				}
				.no_print
				{
					display: none !important;
				}
				#cn{
					background-color: #b2ed63 !important;
				}
				#st{
					background-color: #b2ed63 !important;
				}
				.itm{
					background-color: #b2ed63 !important;
				}
				#com{
					font-size:11px !important;
					font-family:font-family:Khmer OS Muol Light !important;
				}
			}
		</style>
	</head>
	<body>
		<div class="invoice" id="wrap" style="width: 90%; margin: 0 auto;">
			<div class="row">
				<div class="col-lg-12">
					<div class="col-xs-8" style="margin-bottom:10px; margin-top: 14px; padding-right:0;width:70%;">
						<?php if ($logo) { ?>
							<div class="" style="margin-top:10px;float:left;margin-right: 10px;margin-left:-15px;">
								<!--<img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>" alt="<?= $Settings->site_name; ?>">-->
								<img src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>"
									 alt="<?= $biller->company != '-' ? $biller->company : $biller->name; ?>">
							</div>
						<?php } ?>
						<div style="line-height: 1.5;">
							<div style="font-family:Khmer OS Muol Light;" id="com"><?= $biller->cf1;?></div>
							<div style="font-weight:bold;"><?= $biller->company;?></div>
							<div style="font-size:11px;"><?= $biller->cf6;?></div>
							<div style="font-size:11px;"><?= $biller->cf4;?></div>
							<div style="font-size:11px;">ទូរស័ព្ទលេខ : <?= $biller->phone;?></div>
						</div>
					</div>
					<div class="col-xs-4" style="margin-top:14px;width:30%;">
						<div style="font-weight:bold;padding-left:10px;">
							<div style="font-size:18px;">វិក័យប័ត្រ</div>
							<div style="font-size:20px;">INVOICE</div>
						</div>
						<div style="font-size:12px;">Invoice : <?= $inv->reference_no; ?></div>
						<div style="font-size:12px;">Invoice Date : <?= date('M d, Y', strtotime($inv->date)); ?></div>
						<div style="font-size:12px;">User : <?= $user->username?></div>
					</div>
					<div class="row padding10">
						<div class="col-xs-7" style="float:left;margin-top:20px;">
							<table width="80%">
								<thead>
									<tr style="background:#b2ed63;">
										<td style="text-align:left;font-weight:bold;" id="cn">ឈ្មោះអតិថិជន / Customer :</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="border-bottom:0px;">
											<?= $customer->name_kh ? $customer->name_kh.'/' : $customer->company_kh; ?>
											<?= $customer->name ? $customer->name : $customer->company; ?>
										</td>
									</tr>
									<tr>
										<td style="border-top:0px;border-bottom:0px;"><?= $customer->address;?></td>
									</tr>
									<?php
										if($customer->address_kh){
									?>
									<tr>
										<td style="border-top:0px;border-bottom:0px;"><?= $customer->address_kh;?></td>
									</tr>
									<?php
										}
									?>
									
									<tr>
										<td style="border-top:0px;">Customer Group: <?=$customer->customer_group_name;?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="col-xs-5"  style="float: right;margin-top:20px;text-align:right;">
							
                        <?php $br = $this->erp->save_barcode($inv->reference_no, 'code39', 70, false); ?>
                        <img height="45px" src="<?= base_url() ?>assets/uploads/barcode<?= $this->session->userdata('user_id') ?>.png"
                             alt="<?= $inv->reference_no ?>"/>
                        <?php $this->erp->qrcode('link', urlencode(site_url('sales/view/' . $inv->id)), 2); ?>
                        <img height="45px" src="<?= base_url() ?>assets/uploads/qrcode<?= $this->session->userdata('user_id') ?>.png"
                             alt="<?= $inv->reference_no ?>"/>
						</div>
					</div>
					<div><br/></div>
					<div>
						<table style="width: 100%;">
							<thead>
								<tr style="background:#b2ed63; text-align:center; font-weight:bold;" class="itm">
									<td style="text-align:left;padding-left:4px;">Customer ID</td>
									<td>Sale Order ID</td>
									<td colspan="2">Payment Terms</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="text-align:center;"><?= $customer->code; ?></td>
									<td style="text-align:center;"><?= $so_ref->reference_no; ?></td>
									<td colspan="2" style="text-align:center;"><?= $pay_term->description;?></td>
								</tr>
								<tr style="background:#b2ed63; text-align:center; font-weight:bold;" class="itm">
									<td style="text-align:left;padding-left:4px;">Saleman</td>
									<td>Driver</td>
									<td>Ship Date</td>
									<td>Due Date</td>
								</tr>						
								<tr>
									<td style="text-align:center;"><?= $seller->username;?></td>
									<td style="text-align:center;"><?= $deliver_by->name;?></td>
									<td style="text-align:center;"><?= date('d/m/Y', strtotime($inv->date))?></td>
									<td style="text-align:center;"><?= $inv->due_date == 0 ? '': date('d/m/Y', strtotime($inv->due_date)) ;?></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div><br/></div>
					<div>
						<table style="width: 100%;">
							<thead style="font-size: 13px;">
								<tr style="background:#b2ed63;text-align:center;font-weight:bold;" class="itm">
									<td>លេខកូដ<br/>Code</td>
									<td>បរិយាយមុខទំនិញ <br/>Description</td>
									<td>បរិមាណ<br/>Quantity</td>
									<td>ខ្នាត<br/>U/M</td>
									<td>ថ្លៃ​ឯកតា<br/>Unit Price ($)</td>
									<td>បញ្ចុះតម្លៃ<br/>Discount</td>
									<td>ថ្លៃ​ទំនិញ<br/>Amount($)</td>
								</tr>
							</thead>
							<tbody class="sbody" style="font-size: 15.5px;">
								<?php
									$tax_summary = array();
									$i = 0;
                                    $total_quantity = 0;
									foreach ($rows as $row):
									$i++;
                                    $total_quantity += $row->quantity;
									$free = lang('free');
									$product_unit = '';
									if($row->variant){
										$product_unit = $row->variant;
									}else{
										$product_unit = $row->uname;
									}
									$rates = $inv->other_cur_paid_rate;
									
									$product_name_setting;
									if($pos->show_product_code == 0) {
										$product_name_setting = $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : '');
									}else{
										$product_name_setting = $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : '');
									}
									$discount_percentage = '';
									if (strpos($inv->order_discount_id, '%') !== false) {
										$discount_percentage = $inv->order_discount_id;
									}
								?>
								<tr class="no_bd_b">
									<td style="text-align:left; width:6%; vertical-align:middle;">
										<?= $row->product_code?>
									</td>
									<td style="vertical-align:middle;width:33%">
										<?= $product_name_setting ?>
									</td>
									<td style="width: 8%; text-align:right; padding-right:15px; vertical-align:middle;">
										<?= $this->erp->formatQuantity($row->quantity); ?>
									</td>
									<td style="width: 6%; text-align:center; vertical-align:middle;">
										<?php echo $product_unit ?>
									</td>
									<td style="text-align:right; padding-right:30px; width:12%;vertical-align:middle;">
										<?= $row->subtotal!=0?$this->erp->formatMoney($row->unit_price):$free; ?>
									</td>
									<td style="text-align:center; width:5%;vertical-align:middle;">
										<?= ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->erp->formatMoney($row->item_discount); ?>
									</td>
									<td style="text-align:right; padding-right:30px; width:15%;vertical-align:middle;">
										<?= $row->subtotal!=0?$this->erp->formatMoney($row->subtotal):$free; ?>
									</td>
								</tr>
								<?php
									endforeach;
								?>
								<?php
									if($i <=13){
										$n = 13 - $i;
										for($a = 1; $a < $n; $a++){										
								?>
									<tr class="no-bor">
										<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
									</tr>
									<?php
											}
										}
									?>
							</tbody>                    
						</table>
					</div>
					<table style="width:100%">
						<tr valign="top">
							<td rowspan="6" style="border-left:0px;border-bottom:0px;width:25%">
								<p>សមតុល្យសរុប ​​: <?= '$ '. $this->erp->formatMoney($due_amount->bal);?></p>
								<div style="padding-bottom: 10px; float:left;"><?= lang("rate"); ?>= (<?=$this->erp->formatQuantity($rates)?>)&nbsp;&nbsp;<?= lang("qty"); ?>= (<?=$this->erp->formatQuantity($total_quantity)?>)</div>
								<br/>
								<span style="margin-left: -171px; font-size:13px;"><b>* កំណត់សំគាល់ :</b></span>
								</br/>
								<ul>
									<li style="margin-left: -30px;">ទំនិញ​ទិញ​ហើយ មិន​អាច​ដូរ​វិញ​បានទេ</li>
									<li style="margin-left: -30px;">សំរាប់ការទូទាត់ប្រាក់តាម លី ហួរ វេរលុយ</li>
								</ul>
								<span style="font-size:13px !important;"><b>Biller Name :</b> ចំនី​សត្វ​សានណា​ហ្គ្រោ</span><br/>
								<span style="font-size:13px !important;"><b>Biller Code  : </b>  59008</span>
							</td>
							<td style="width:28.3%">សរុបថ្លៃទំនិញ  / Sub Total (USD/KHR)</td>
							<td style="width:19%">
								<?= $this->erp->formatMoney($inv->total); ?>
								(<?= $this->erp->formatMoney(($inv->total)*$rates); ?> ​​​៛)
							</td>
						</tr>
						<tr>
							<td style="width:28.3%">ថ្លៃដឹកជញ្ញូន / Freight (USD/KHR)</td>
							<td style="width:19%">
								<?= $this->erp->formatMoney($inv->shipping);?>
								(<?= $this->erp->formatMoney(($inv->shipping)*$rates);?>  ៛)
							</td>
						</tr>
						<tr>
							<td style="width:28.3%">បញ្ចុះតម្លៃ / Discounts</td>
							<td style="width:19%"><?= ($discount_percentage?"(" . $discount_percentage . ")" : '').'</span>' . $this->erp->formatMoney($inv->order_discount);?></td>
						</tr>
						<tr>
							<td style="width:28.3%">ទឹកប្រាក់បង់ / Payment (USD/KHR)</td>
							<td style="width:19%">
								<?= $this->erp->formatMoney($inv->paid); ?>
								(<?= $this->erp->formatMoney(($inv->paid)*$rates); ?> ៛)
							</td>
						</tr>
						<tr style="background:#b2ed63;font-weight:bold;height: 28px;font-size:13px !important;" class="itm">
							<td style="width:28.3%"><strong><?= lang("សមតុល្យ​ជាប្រាក់រៀល / Balance​​ in KHR"); ?><strong></td>
							<td style="width:19%">
								<strong>
									<?php
										echo $this->erp->formatMoney(abs(($inv->grand_total - $inv->paid)*$rates)).' ៛'; 
									?>
								</strong>
							</td>
						</tr>
						<tr style="background:#b2ed63; font-weight:bold; height: 28px;font-size:13px !important;" class="itm">
							<td style="width:28.3%; vertical-align:middle !important;"><strong><?= lang("សមតុល្យ​ជាប្រាក់ដុល្លា / Balance in USD"); ?></strong></td>
							<td style="width:19%">
								<strong>
									<?= $this->erp->formatMoney(abs($inv->grand_total - $inv->paid)) ; ?> $
								</strong>
							</td>
						</tr>
					</table>
					<div class="row">
						<div class="col-xs-3" style="text-align:center">
							<hr style="border:dotted 1px; width:100px; vertical-align:bottom !important; ">
							<p>អតិថិជន</p>
							<p>Customer</p>
						</div>
						<div class="col-xs-2" style="text-align:center">
							<hr style="border:dotted 1px; width:100px; vertical-align:bottom !important; ">
							<p>អ្នកដឹកជញ្ជូន</p>
							<p>Deliverer</p>
						</div>
						<div class="col-xs-2" style="text-align:center">
							<hr style="border:dotted 1px; width:100px; vertical-align:bottom !important; ">
							<p>អ្នកវេចខ្ចប់ទំនិញ</p>
							<p>Store Keeper</p>
						</div>
						<div class="col-xs-2" style="text-align:center">
							<hr style="border:dotted 1px; width:100px; vertical-align:bottom !important; ">
							<p>អ្នកលក់</p>
							<p>Seller</p>
						</div>
						<div class="col-xs-3" style="text-align:center">
							<hr style="border:dotted 1px; width:100px; vertical-align:bottom !important; ">
							<p>ប្រធានចាត់ការទូទៅ</p>
							<p>General Manager</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="wrap" style="width: 90%; margin:0px auto;" class="no_print">
			<div class="col-xs-10" style="margin-bottom:20px;">
				<button type="button" class="btn btn-primary btn-default no-print pull-left" onclick="window.print();">
					<i class="fa fa-print"></i> <?= lang('print'); ?>
				</button>&nbsp;&nbsp;
				<a href="<?= site_url('sales'); ?>"><button class="btn btn-warning no-print" ><i class="fa fa-heart"></i>&nbsp;<?= lang("back_to_sale"); ?></button></a>
			</div>
		</div>
		<script type="text/javascript">
			window.onload = function() { window.print(); }
		</script>
	</body>
</html>