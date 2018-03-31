
 <?php
	//$sale->total = $this->erp->convertCurrency($sale_item->currency_code, $setting->default_currency, $sale->total);
	//$this->erp->print_arrays($services);
?>
 <style type="text/css">
    .container {
        width: 800px;
        margin-left: auto;
        margin-right: auto;
	}	
	.t_c{text-align:center;}
	.t_r{text-align:right;}
    @media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
		
	}
	.kh_m{
		font-family: "Khmer OS Muol";
	}
	.b_top{
		border-top:1px solid black;
		margin-bottom: 20px;
		max-width: 100%;
		width: 100%;
		}
	.b_bottom{border-bottom:1px solid black}
	.b_left{border-left:1px solid black;}
	.b_right{border-right:1px solid black;}
	.text-bold td{font-weight:bold;}
	.p_l_r td{padding-left:5px;padding-right:5px;}
	.top_info tr td{
		height:25px;
	}
	.color_blue{color:#3366cc;}
	.color_blue{color:#3366cc;}
	#logo img{
		width:110px;
	}
	.table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td{
		border:none;
		border: 1px ;
		padding:4px;
	}
	
	
</style>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('payment_schedule'); ?></h4>
        </div>
        <div class="modal-body">
			<div class="row">
				<div class="container">
					<div>
						<div style="float:left;width:25%;" id="logo">
							<span>  </span> 
						</div>
						<div style="float:left;width:50%; font-family:Battambang;">	
							<center><b>
								<span class="kh_m"><b>  </b></span><br/>
								<span> <?= lang("branch_company_name") ?> : <?php //$this->session->branchName; ?></span><br/>
								<span style="font-size:18px;;"> <?= lang("installments") ?> </span><br/>
							</center></b>
						</div>
						<div style="float:left;width:25%;">
							<center><span style="line-height:140%; font-size:12px;"><?=lang("agree_to_pay_by_schedule")?> <br/><?=lang("date") ?>: <?= $this->erp->hrsd(date('Y-m-d')); ?><br/><?= lang("right_thumbprints") ?></span></center>
						</div>
					</div>
					
					<div>
						
						<table class="b_top" style="font-size:11px; border:none">
							<tbody>
							  <tr>
								<td>  <?= lang("customer_name") ?></td>
								<td class="color_blue">: <b><?= $customer->name; ?></b></td>
								<td>  <?= lang("customer_latin_name") ?></td>
								<td class="color_blue">: <?= $customer->name; ?></td>
								<td rowspan="5" style="width:90px; border:1px solid grey;"></td>
								<td rowspan="5" style="width:90px; border:1px solid grey;"></td>
							  </tr>
							  <tr>
								<td><?= lang("account_number") ?></td>
								<td class="color_blue">: <?php //$sale->reference_no;?> </td>
								<td><?= lang("c_o_name") ?></td>
								<td class="color_blue">: <?php // $creator->first_name . ' ' . $creator->last_name ; ?></td>
								
							  </tr>
							  <tr>
								<td><?= lang("phone1") ?></td>
								<td class="color_blue">: <b> <?= $customer->phone; ?>  </b></td>
								<td><?= lang("c_o_phone") ?></td>
								<td class="color_blue">:  <?= $customer->phone; ?> </td>
								
							  </tr>
							  <tr>
								<td><?= lang("disburse_date") ?></td>
								<td class="color_blue">:</td>
								<td><?= lang("part") ?></td>
								<td class="color_blue">: 1</td>
								
							  </tr>
							  <tr>
								<td><?= lang("term_loan") ?></td>
								<td class="color_blue">:</td>
								<td><?= lang("penalty") ?></td>
								<td class="color_blue">: ​​​​​​​​ </td>
								
							  </tr>
							  <tr>
								<td><?= lang("total_balance_schedule") ?></td>
								<td class="color_blue">:</td>
								<td><?= lang("collateral_schedule") ?></td>
								<td class="color_blue">: </td>
								<td style="text-align:center"><b></b></td>
								<td style="text-align:center"><b><?= lang("photo") ?></b></td>
							  </tr>
							  <tr>
								<td><?= lang("interest_rate_schedule") ?></td>
								<td class="color_blue">: </td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							  </tr>
							<?php
								
							?>
							  <tr>
								<td><?= lang("address_approved") ?></td>
								<td colspan="4">:</td>
								<td></td>
							  </tr>
							</tbody>
						  </table>
					</div>					
					<table style="font-size:11px;border-collapse:collapse;width:100%;" class="schedule">
						<tr class="p_l_r" style="background-color:#009900;color:white;">
							<?php 
								
							?>							
							<td  class="t_c" style="width: 25px;"> <?= lang("no") ?> </td>
							<td  class="t_c" style="width: 104px;"><?= lang("intallment_date") ?></td>
							<td  class="t_c" style="width: 85px;"><?= lang("principle_paid") ?></td>
							<td  class="t_c" style="width: 78px;"><?= lang("interest_paid") ?></td>
							<?php
							
							?>
							<td  class="t_c" style="width: 90px;"><?= lang("principle_balance") ?></td>
							<td  class="t_c" style="width: 92px;"><?= lang("total_intallment") ?></td>
							<td  class="t_c" style="padding: 5px; width: 100px;"><?= lang("action") ?></td>
						</tr>						
						<?php
							
						?>	
						<tr class=" text-bold">
							<td class="t_c" style="padding-left: 5px; padding-right: 5px; height: 25px;" colspan="3"><?= lang("total_schedule") ?></td>
							<td class="t_r" style="padding-left:5px;padding-right:5px;"></td>
							<td class="t_r" style="padding-left:5px;padding-right:5px;"></td>
							<?php
								
							?>
							
							<td></td>
							<td class="t_r" style="padding-left:5px;padding-right:5px;"></td>
							<td></td>
							
						</tr>
						
					</table>
					<div style="margin-top: 10px; margin-bottom: 10px;">
						<table style="font-size:11px;">
							<tr>
								<td style="width:110px;"><b> <?= lang("note") ?>:</b> <td>
								<td><?= lang("payment_note")?><td>
							</tr>
							<tr>
								<td><td>
								<td>- <?=lang("the_contract_does_not_comply")?> <b> </b> &nbsp <?= lang("company_will_take_legal_action") ?></td>
							</tr>
							
						</table>
					</div>

				</div>
			</div>
        </div>
        
		<div class="buttons">
			<div class="btn-group btn-group-justified no-print">
				<?php if ($this->Owner || $this->Admin || $this->permission['payment-add']) { ?> 
					<div class="btn-group">
						<a href="#" data-toggle="modal" data-target="#myModal2" class="add_payment tip btn btn-primary" id="add_payment" title="<?= lang('add_payment') ?>">
							<i class="fa fa-money"></i>
							<span class="hidden-sm hidden-xs"><?= lang('add_payment') ?></span>
						</a>
					</div>
				<?php } ?>
				<div class="btn-group">
					<a href="#" data-toggle="modal" data-target="#myModal2" class="change_date tip btn btn-primary" title="<?= lang('change_date') ?>">
						<i class="fa fa-edit"></i>
						<span class="hidden-sm hidden-xs"><?= lang('change_date') ?></span>
					</a>
				</div>
				<div class="btn-group">
					<a href="#" data-toggle="modal" data-target="#myModal2" class="pdf tip btn btn-primary" id="pdf" title="<?= lang('add_payment') ?>">
						<i class="fa fa-money"></i>
						<span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
					</a>
				</div>
				<div class="btn-group">
					<a href="#" data-toggle="modal" data-target="#myModal2" class="excel tip btn btn-primary" id="excel" title="<?= lang('add_payment') ?>">
						<i class="fa fa-money"></i>
						<span class="hidden-sm hidden-xs"><?= lang('excel') ?></span>
					</a>
				</div>
				<div class="btn-group">
					<a class="tip btn btn-warning" title="<?= lang('print') ?>" onclick="window.print();">
						<i class="fa fa-print"></i>
						<span class="hidden-sm hidden-xs"><?= lang('print') ?></span>
					</a>
				</div>
			</div>
        </div>
    </div>
</div>
<?= isset($modal_js) ?$modal_js  : ('') ?>
