<?php //$this->erp->print_arrays($Settings);?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->lang->line("sales_invoice") . " " . $inv->reference_no; ?></title>
    <link href="<?php echo $assets ?>styles/theme.css" rel="stylesheet">
    <style type="text/css">
        html, body {
            height: 100%;
            background: #FFF;
        }

        body:before, body:after {
            display: none !important;
        }

        .table th {
            text-align: center;
            padding: 5px;
        }

        .table td {
            padding: 4px;
        }
		hr{
			border-color: #333;
			width:100px;
			margin-top: 70px;
		}
        @media print,screen{
            body {
                width: 100%;
            }
        }
    </style>
</head>

<body>
<div class="print_rec" id="wrap" style="width:1024px; margin: 0 auto;">
    <div class="row">
        <div class="col-lg-12">
            <?php if (isset($biller->logo)) { ?>
				<div class="col-xs-3 text-center" style="margin-bottom:20px;">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>"
                         alt="<?= $biller->company != '-' ? $biller->company : $biller->name; ?>">
                </div>
			 <?php }else{ ?>
				<div class="col-xs-3 text-center" style="margin-bottom:20px;"></div>
			 <?php } ?>
                <div class="col-xs-6 text-center">
                    <h2 style="font-family: Khmer Os Moul"><?= lang("invoice_kh"); ?></h2>
                    <h2><?= lang("sales_receipt"); ?></h2>
                </div>
                <div class="col-xs-3"></div>
           
            <div class="clearfix"></div>
            <br>
            <div class="row padding10">
                <div class="col-xs-5" style="float: left;font-size:14px">
                    <table>
                        <tr>
                            <td>
                                <p><b><?= lang("name");?></b></p>
                            </td>
                            <td>
                               <p>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</p>
                            </td>
                            <td>
                                <p><?= $inv->customer?></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><b><?= lang("address");?></b></p>
                            </td>
                            <td>
                                <p>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</p>
                            </td>
                            <td>
                                <p><?= $customer->address ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><b><?= lang("phone");?></b></p>
                            </td>
                            <td>
                                <p>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</p>
                            </td>
                            <td>
                                <p><?= $customer->phone ?></p>
                            </td>
                        </tr>
                         <tr>
                            <td>
                                <p><b><?= lang("email");?></b></p>
                            </td>
                            <td>
                                <p>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</p>
                            </td>
                            <td>
                                <p><?= $customer->email ?></p>
                            </td>
                        </tr>
                    </table>
                    <div class="clearfix"></div>
                </div>
                <div class="col-xs-2">
                    
                </div>
                <div class="col-xs-5"  style="float: right;font-size:14px">
                    <table>
                        <tr>
                            <td>
                                <p><b><?= lang("date_kh"); ?></b></p>
                            </td>
                            <td>
                                <p>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</p>
                            </td>
                            <td>
                                <p><?= $this->erp->hrsd($inv->date);?></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><b><?= lang("number_kh"); ?></b></p>
                            </td>
                            <td>
                                <p>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</p>
                            </td>
                            <td>
                                <p><?= "<b>".$inv->reference_no."</b>";?></p>
                            </td>
                        </tr>
						<tr>
                            <td>
                                <p><b><?= lang("saleman_kh"); ?></b></p>
                            </td>
                            <td>
                                <p>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</p>
                            </td>
                            <td>
                                <p><?= "<b>".$inv->saleman."</b>";?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
			<div><br/></div>
            <div class="-table-responsive">
                <table class="table table-bordered table-striped" style="width: 100%;">
                    <thead  style="font-size: 13px;">
						<tr>
							<th><?= lang("ល.រ"); ?><br />No</th>
                            <th><?= lang("code_kh"); ?><br><?= lang("code");  ?></th> 
                            <th><?= lang("description_kh"); ?><br><?= lang("descript");  ?></th> 
                            <th><?= lang("unit_kh"); ?><br><?= lang("uom");  ?></th> 
                            <th><?= lang("number_kh"); ?><br><?= lang("piece");  ?></th> 
                            <th><?= lang("length_piecs_kh"); ?><br><?= lang("length_piecs");  ?></th>
                            <th><?= lang("qty_kh"); ?><br><?= lang("qty");  ?></th>
							<?php if($Owner || $Admin || $GP['sales-price']){ ?>
								<th><?= lang("unit_price_kh"); ?><br><?= lang("unit_price");  ?></th>
							<?php }?>
                            <th><?= lang("total_kh"); ?><br><?= lang("total_capital");  ?></th> 
						</tr>
                    </thead>
                    <tbody>
                        <?php $r = 1;
                        $tax_summary = array();
						$grand_total = 0;
                        foreach ($rows as $row):

                                $str_unit = "";
                                $grand_total += ($row->quantity)*($row->unit_price);
                                if($row->option_id){
                                    $var = $this->sales_model->getVar($row->option_id);
                                    $str_unit = $var->name;
                                }else{
                                    $str_unit = $row->unit;
                            }
                        ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code ?>
                                </td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_name ?>
                                </td>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $str_unit ?></td>
                                <td class="text-center">
                                    <?= $this->erp->formatMoney($row->piece) ?>
                                </td>
                                <td class="text-center">
                                    <?= $this->erp->formatMoney($row->wpiece) ?>
                                </td>
                                 <td style="text-align:center; vertical-align:middle;">
                                    <?= $this->erp->formatMoney($row->quantity) ?>
                                </td>
								<?php if($Owner || $Admin || $GP['sales-price']){ ?>
									<td class="text-center">
										<?= $this->erp->formatMoney($row->unit_price) ?>
									</td>
								<?php }?>
                                <td class="text-right">
                                    <?= $this->erp->formatMoney(($row->quantity)*($row->unit_price)) ?>
                                </td>
                               
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                        ?>
                        <?php
                        $col = 3;
                        $rows = 5;
                        if($Owner || $Admin || $GP['purchases-cost']){
                            $col++;
                        }
                        if ($inv->sale_status == 'partial') {
                            $col++;
                        }
                        if ($Settings->product_discount) {
                            $col++;
                        }
                        if ($Settings->tax1) {
                            $col++;
                        }
                        if ($Settings->product_discount && $Settings->tax1) {
                            $tcol = $col - 2;
                        } elseif ($Settings->product_discount) {
                            $tcol = $col - 1;
                        } elseif ($Settings->tax1) {
                            $tcol = $col - 1;
                        } else {
                            $tcol = $col;
                        }

                        if ($inv->order_discount != 0) {
                            $rows++;
                        }
                        ?>
                        <tr>
							<?php if($Owner || $Admin || $GP['sales-price']){?>
								<td colspan="7" rowspan="<?= $rows; ?>" style="border-left: 1px solid #FFF !important;​border-bottom: 1px solid #FFF !important">
									<?php
									if ($inv->note || $inv->note != "") { ?>
										<div>
											<p><b><?= lang("note"); ?>:</b></p>
											<div><?= $this->erp->decode_html($inv->note); ?></div>
										</div>
									<?php
									}
									?>
								</td>
							<?php }else{?>
								<td colspan="6" rowspan="<?= $rows; ?>" style="border-left: 1px solid #FFF !important; border-bottom: 1px solid #FFF !important">
									<?php
									if ($inv->note || $inv->note != "") { ?>
										<div>
											<p><b><?= lang("note"); ?>:</b></p>
											<div><?= $this->erp->decode_html($inv->note); ?></div>
										</div>
									<?php
									}
									?>
								</td>
							<?php } ?>
                            <td style="text-align:right; font-weight:bold;"><?= lang("total_kh"); ?>
                            </td>
                            <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= "$ ".$this->erp->formatMoney($grand_total); ?></td>
                        </tr>
						<?php if($inv->order_discount != 0){ ?>
							<tr>
								<td style="text-align:right; font-weight:bold;"><?= lang("discount_kh"); ?>
								</td>
								<td style="text-align:right; padding-right:10px; font-weight:bold;"><?= "$ ".$this->erp->formatMoney($inv->order_discount); ?></td>
							</tr>
						<?php } ?>
                        <tr>
                            <td style="text-align:right; font-weight:bold;"><?= lang("totalpaid_kh"); ?>
                            </td>
                            <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= "$ ".$this->erp->formatMoney($grand_total-$inv->total_discount); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right; font-weight:bold;"><?= lang("deposit_kh"); ?>
                            </td>
                            <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= "$ ".$this->erp->formatMoney($inv->paid); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right; font-weight:bold;"><?= lang("balance_kh"); ?>
                            </td>
                            <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= "$ ".$this->erp->formatMoney(($grand_total-$inv->total_discount)-$inv->paid); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <center>
                    <hr style="border:dotted 1px; width:200px;" />
                    <p><?= lang('​ហត្ថលេខា និង ឈ្មោះ​អ្នក​ទិញ​  <br/> Customer`s Signature & Name'); ?></p>
                    </center>
                </div>
                <div class="col-sm-6">
                    <center>
                    <hr style="border:dotted 1px; width:200px;" />
                    <p><?= lang('​ហត្ថលេខា និង ឈ្មោះ​អ្នក​លក់​  <br/> Seller`s Signature & Name'); ?></p>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>

<br/><br/>
<div></div>
<!--<div style="margin-bottom:50px;">
	<div class="col-xs-4" id="hide" >
		<a href="<?= site_url('sales'); ?>"><button class="btn btn-warning " ><?= lang("Back to AddSale"); ?></button></a>&nbsp;&nbsp;&nbsp;
		<button class="btn btn-primary" id="print_receipt"><?= lang("Print"); ?>&nbsp;<i class="fa fa-print"></i></button>
	</div>
</div>-->
<script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $(document).on('click', '#b-add-quote' ,function(event){
    event.preventDefault();
    localStorage.removeItem('slitems');
    window.location.href = "<?= site_url('purchases_request/add'); ?>";
  });
  $(document).on('click', '#b-view-pr' ,function(event){
    event.preventDefault();
    localStorage.removeItem('slitems');
    window.location.href = "<?= site_url('purchases_request/index'); ?>";
  });
});

</script>
</body>
</html>
