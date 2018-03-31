<style type="text/css">
    @media print {
        #myModal{
            display: none !important;
        }
		.modal-content, .modal-body, .main_table{
				font-size : 13px !important;
				border-bottom: none !important;
				border-top: none !important;
				border-right: none !important;
				border-left: none !important;
				width : 100% !important;
				margin-left : 0px !important;
				margin-right : 0px !important;
		}
		.no-modal-header{
			margin-top : -10px !important;
		}
		#th-header th{
			color : #FFFFFF !important;
			background-color: #428BCA !important;
			
		}
		#rec_sign{
			float : right !important;
		}
		.div_logo{
			margin-left: -120px !important;
		}
    }
</style>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body print">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
			<button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            
            <div class="row">
                <div class="col-xs-12 div_logo" style="margin-left: -100px;">					
                    <div class="col-sm-8 col-xs-8 pull-right" style="padding: 0 !important; margin-bottom:10px; width:70%;">
						<?php if ($logo) { ?>
							<div class="logo" style="margin-top:10px;float:left;margin-right: 10px;margin-left:-25px;">
								<img style="margin-top: -10px;" src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>"
									 alt="<?= $biller->company != '-' ? $biller->company : $biller->name; ?>">
							</div>
						<?php } ?>
						<div style="line-height: 1.5;">
							<div style="font-family:Khmer OS Muol Light;" id="com"><?= $biller->cf1;?></div>
							<div style="font-weight:bold;"><?= $biller->company;?></div>
							<div style="font-size:11px;"><?= $biller->cf4;?></div>
							<div style="font-size:11px;">ទូរស័ព្ទលេខ : <?= $biller->phone;?></div>
						</div>
					</div>
                </div>
            </div>
			
			<br/>
			<div class="clearfix"></div>
            <div class="row" id="info_detail">
                <div class="col-xs-6">
                    <p><b><?= lang("payment_reference"); ?></b>: <?= $payment->reference_no; ?></p>
					<p><b><?= lang("date_invoice"); ?></b>: <?= $this->erp->hrld($inv->date); ?></p>
					<p><b><?= lang("date_received"); ?></b>: <?= $this->erp->hrld($payment->date); ?></p>
                </div>
				<div class="col-xs-6 pull-right">
					<div class="pull-right">
						<p><b><?= lang("invoice_no"); ?></b>: <?= $inv->reference_no; ?></p>
						<p><b><?= lang("username"); ?></b>: <?= $this->session->userdata('username'); ?></p>
						<p><b><?= lang("customer"); ?></b>: <?= $inv->customer; ?></p>
					</div>
                </div>
            </div>
			<div class="row">
				<div class="col-sm-12 text-center">
					<strong><p style="font-size:16px; font-family:Khmer OS System">
						បណ្ណ័ទទួលប្រាក់​<br>Receipt Vocher
					</p></strong>
				</div>
			</div>
            <div class="main_table">
				<table class="table receipt">
					<thead id = "th-header">
						<tr>
							<th class="text-left" style="text-align : left !important"><?= lang("no"); ?></th>
							<th><?= lang("description"); ?></th>
							<th><?= lang("qty"); ?></th>
							<th><?= lang("price"); ?></th>
							<?php if ($inv->total_discount != 0 || $total_disc != '') {
								echo '<th>'.lang('discount').'</th>';
							} ?>
							<th class="text-right" style="padding-left:10px;padding-right:10px;text-align : right !important"><?= lang("amount"); ?> </th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no = 1;
						$m_us = 0;
						$total_quantity = 0;
						foreach($rows as $row){
							$free = lang('free');
							
							echo '<tr class="item"><td class="text-left">#' . $no . "</td>";
							echo '<td class="text-center">' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : '') . '</td>';
							echo '<td class="text-center">' . $this->erp->formatQuantity($row->quantity);
							
							echo '<td class="text-center">' . '$ '. $this->erp->formatMoney($row->real_unit_price) . '<br/>(' .$this->erp->formatMoney($row->real_unit_price * $row->other_cur_paid_rate).')' .'</td>';
							$colspan = 5;
							if ($inv->order_discount != 0 || $row->item_discount != 0) {
								echo '<td class="text-center">';
								echo '<span>' ;
									if(strpos($row->discount, '%') !== false){
										echo $row->discount;
									}else{
										echo $row->discount;
									}
									
								echo '</span> ';
								$colspan = 5;
								$total_col = 3;
								echo '</td>';
							}else{
								if($total_disc != ''){
									echo '<td class="text-center"></td>';
									$colspan = 5;
									$total_col = 3;
								}else{
									$colspan = 4;
									$total_col = 2;
								}
							}
							echo '<td class="text-right">' . ($this->erp->formatMoney($row->subtotal) == 0 ? $free:'$ '. $this->erp->formatMoney($row->subtotal). '<br/>(' .$this->erp->formatMoney($row->subtotal * $row->other_cur_paid_rate).')') . '</td>';
							$no++;
							$total_quantity += $row->quantity;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2"><?= lang("rate"); ?> : <?php echo $exchange_rate_kh_c->rate ? number_format($exchange_rate_kh_c->rate):0 ?> ៛ | <?= lang("qty"); ?>= (<?=$this->erp->formatQuantity($total_quantity)?>)</th>
							<th colspan="<?=$total_col?>" class="text-right"><?= lang("total"); ?></th>
							<th class="text-right"><?= '$ '. $this->erp->formatMoney($inv->total + $inv->product_tax). '<br/>(' .$this->erp->formatMoney(($inv->total + $inv->product_tax) * $inv->other_cur_paid_rate).')'; ?></th>
						</tr>
						<tr colspan="5">
							<table class="table table-striped">
								<tbody>
									<tr>
										<td class="text-left" width="30%">
											<strong><?= lang("current_balance"); ?></strong>
										</td>
										<td><strong><?php echo '$ '. $this->erp->formatMoney($curr_balance) .' ( ' . $this->erp->formatMoney($curr_balance * $inv->other_cur_paid_rate).' )' ; ?></strong></td>
									</tr>
									
									<?php if($payment->extra_paid != 0) { ?>
									<tr>
										<td class="text-left" width="30%">
											<strong><?= lang("paid"); ?></strong>
										</td>
										<td><strong><?php echo '$ '. $this->erp->formatMoney(($payment->amount-$payment->extra_paid)).' ( ' . $this->erp->formatMoney(($payment->amount - $payment->extra_paid) * $inv->other_cur_paid_rate).' )'; ?></strong></td>
									</tr>
									<tr>
										<td class="text-left" width="30%">
											<strong><?= lang("extra_paid"); ?></strong>
										</td>
										<td><strong><?php echo '$ '. $this->erp->formatMoney($payment->extra_paid); ?></strong></td>
									</tr>
									<?php } ?>
									<tr>
										<td class="text-left" width="30%">
											<strong><?= $payment->type == 'returned' ? lang("payment_returned") : lang("payment_received"); ?></strong>
										</td>
										<td>
											<strong>
												<?php echo '$ '. $this->erp->formatMoney($payment->amount).' ( ' . $this->erp->formatMoney($payment->amount * $inv->other_cur_paid_rate).' )' .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ( '. $this->erp->convert_number_to_words(($this->erp->fraction($payment->amount) > 0)? $this->erp->formatMoney($payment->amount) : number_format($payment->amount)) .' dolar )'; ?>
											</strong>
										</td>
									</tr>
									
									<tr>
										<td class="text-left" width="30%">
											<strong><?= lang("balance"); ?></strong>
										</td>
										<td><strong><?php echo '$ '. $this->erp->formatMoney($curr_balance - ($payment->amount - $payment->extra_paid)).' ( ' . $this->erp->formatMoney(($curr_balance - ($payment->amount - $payment->extra_paid)) * $inv->other_cur_paid_rate).' )'; ?></strong></td>
									</tr>
									<tr>
										<td><strong><?= lang("paid_by"); ?></strong></td>
										<td>
											<strong> : 
												<?php echo lang($payment->paid_by);
													if ($payment->paid_by == 'gift_card' || $payment->paid_by == 'CC') {
														echo ' (' . $payment->cc_no . ')';
													} elseif ($payment->paid_by == 'Cheque') {
														echo ' (' . $payment->cheque_no . ')';
													}
												?>
											</strong>
										</td>
									</tr>
									<tr>
										<td>
											<strong><?= lang("note"); ?></strong>
										</td>
										<td><strong><?php echo $payment->note; ?></strong></td>
									</tr>
								</tbody>
							
							</table>
						</tr>
					</tfoot>
				</table>
			<!--
                <table class="table table-borderless" style="margin-bottom:0;">
                    <tbody>
                    <tr>
						<tr>
							<td>
								<strong><?= lang("current_balance"); ?></strong>
							</td>
							<td><strong> : <?php echo $this->erp->formatMoney($curr_balance); ?></strong></td>
						</tr>
						
						<?php if($payment->extra_paid != 0) { ?>
						<tr>
							<td>
								<strong><?= lang("paid"); ?></strong>
							</td>
							<td><strong> : <?php echo $this->erp->formatMoney(($payment->amount-$payment->extra_paid)); ?></strong></td>
						</tr>
						<tr>
							<td>
								<strong><?= lang("extra_paid"); ?></strong>
							</td>
							<td><strong> : <?php echo $this->erp->formatMoney($payment->extra_paid); ?></strong></td>
						</tr>
						<?php } ?>
                        <td width="25%">
                            <strong><?= $payment->type == 'returned' ? lang("payment_returned") : lang("payment_received"); ?></strong>
                        </td>
                        <td>
							<strong> : 
								<?php echo '$ '. $this->erp->formatMoney($payment->amount) .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ( '. $this->erp->convert_number_to_words(($this->erp->fraction($payment->amount) > 0)? $this->erp->formatMoney($payment->amount) : number_format($payment->amount)) .' dolar )'; ?>
							</strong>
                        </td>
						<tr>
							<td>
								<strong><?= lang("balance"); ?></strong>
							</td>
							<td><strong> : <?php echo $this->erp->formatMoney($curr_balance - ($payment->amount - $payment->extra_paid)); ?></strong></td>
						</tr>
                    </tr>
                    <tr>
                        <td><strong><?= lang("paid_by"); ?></strong></td>
                        <td>
							<strong> : 
								<?php echo lang($payment->paid_by);
									if ($payment->paid_by == 'gift_card' || $payment->paid_by == 'CC') {
										echo ' (' . $payment->cc_no . ')';
									} elseif ($payment->paid_by == 'Cheque') {
										echo ' (' . $payment->cheque_no . ')';
									}
                                ?>
							</strong>
						</td>
                    </tr>

					 <tr>
                        <td>
                            <strong><?= lang("note"); ?></strong>
                        </td>
                        <td><strong><?php echo $payment->note; ?></strong></td>
                    </tr>
                    </tbody>
                </table>
				-->
            </div>
			<p class="alert text-center"><?= $this->erp->decode_html($biller->invoice_footer); ?></p>
            <div style="clear: both;"></div>
            <div class="row">
				<div class="col-sm-4 pull-left">
                    <p>&nbsp;</p>
                    <p style="border-bottom: 1px solid #666;">&nbsp;</p>
                    <p>ហត្ថលេខា អតិថិជន</p>
                </div>
				<div class="col-sm-4 pull-left">
                </div>
                <div class="col-sm-4 pull-left" id = "rec_sign">
                    <p>&nbsp;</p>
                    <p style="border-bottom: 1px solid #666;">&nbsp;</p>
                    <p>ហត្ថលេខា​ បេឡា</p>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>