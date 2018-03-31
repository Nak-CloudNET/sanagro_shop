<?php
	//$this->erp->print_arrays($inv);
?>

<script type="text/javascript">
    var count = 1, an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
        tax_rates = <?php echo json_encode($tax_rates); ?>;
    //var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
    //var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if ($inv) { ?>
        localStorage.setItem('sldate', '<?= $this->erp->hrld($inv->date) ?>');
        localStorage.setItem('slcustomer', '<?= $inv->customer_id ?>');
        localStorage.setItem('slbiller', '<?= $inv->biller_id ?>');
        localStorage.setItem('slref', '<?= $inv->reference_no ?>');
        localStorage.setItem('slwarehouse', '<?= $inv->warehouse_id ?>');
        localStorage.setItem('slsale_status', '<?= $inv->sale_status ?>');
        localStorage.setItem('slpayment_status', '<?= $inv->payment_status ?>');
        localStorage.setItem('slpayment_term', '<?= $inv->payment_term ?>');
        localStorage.setItem('slnote', '<?= str_replace(array("\r", "\n"), "", $this->erp->decode_html($inv->note)); ?>');
        localStorage.setItem('slinnote', '<?= str_replace(array("\r", "\n"), "", $this->erp->decode_html($inv->staff_note)); ?>');
        localStorage.setItem('sldiscount', '<?= $inv->order_discount_id ?>');
        localStorage.setItem('sltax2', '<?= $inv->order_tax_id ?>');
        localStorage.setItem('slshipping', '<?= $inv->shipping ?>');
        localStorage.setItem('slitems', JSON.stringify(<?= $inv_items; ?>));
		 <?php if (isset($payment->paid_by)) { ?>
		localStorage.setItem('paid_by_1', '<?= $payment->paid_by ?>');
		localStorage.setItem('paid', '<?= $payment->amount ?>');
		localStorage.setItem('deposited', '<?= $payment->amount ?>');
		<?php } ?>
        <?php } ?>
		
		if(localStorage.getItem('quote_ID')){
			localStorage.removeItem('quote_ID');
		}
        <?php if ($Owner || $Admin) { ?>
        $(document).on('change', '#sldate', function (e) {
            localStorage.setItem('sldate', $(this).val());
        });
        if (sldate = localStorage.getItem('sldate')) {
            $('#sldate').val(sldate);
        }
        $(document).on('change', '#slbiller', function (e) {
            localStorage.setItem('slbiller', $(this).val());
        });
        if (slbiller = localStorage.getItem('slbiller')) {
            $('#slbiller').val(slbiller);
        }
        <?php } ?>
        ItemnTotals();
        $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#slcustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above');?>');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= site_url('sales/suggestionsSale'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#slwarehouse").val(),
                        customer_id: $("#slcustomer").val()
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 200,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    // $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    // $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });
        $('#add_item').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                $(this).autocomplete("search");
            }
        });

        $(window).bind('beforeunload', function (e) {
            localStorage.setItem('remove_slls', true);
            if (count > 1) {
                var message = "You will loss data!";
                return message;
            }
        });
        $('#reset').click(function (e) {
            $(window).unbind('beforeunload');
        });
        $('#edit_sale').click(function () {
			<?php if($setting->credit_limit == 1) {?>
			var payment_status = $("#slpayment_status").val();
			if(payment_status == 'due' || payment_status == 'partial'){				
				var customer_id = $('#slcustomer').val();
				var c_balance= localStorage.getItem('cust_balance');
				var c_limit= localStorage.getItem('credit_limit');												
				var cust_balance = $('#total_balance').val()-0;				
				cust_balance+= parseFloat(c_balance);				
				if(c_limit >= 0 && c_limit < cust_balance){					
					if (confirm("This customer has over credit limit ("+(cust_balance - c_limit)+"$)!\n Your Balance is "+cust_balance+"$\n Your Credit Balance is "+parseFloat(c_limit)+"$\n Click (OK) if you want to continue  Or Click (Cancel) if you want to Cancel Adding.") == true) {
						
					} else {
						
						return false;
					}
					
				}
			}
			<?php } ?>
            $(window).unbind('beforeunload');
            $('form.edit-so-form').submit();
        });
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_sale'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-so-form');
                echo form_open_multipart("sales/edit/" . $inv->id, $attrib)
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($Owner || $Admin) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("date", "sldate"); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->erp->hrld($inv->date)), 'class="form-control input-tip datetime" id="sldate" required="required"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("sale_ref", "slref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control input-tip" id="slref" required="required" style="pointer-events:none;"'); ?>
                            </div>
                        </div>
						<?php if($sale_order && $sale_order->reference_no) { ?>
						<div class="col-md-4">
							<div class="form-group">
								<?= lang("so_no", "soref"); ?>
								<?php echo form_input('so_reference_no', (isset($_POST['so_reference_no']) ? $_POST['so_reference_no'] : $sale_order->reference_no), 'class="form-control input-tip" id="soref" style="pointer-events:none;"'); ?>
								<input type="hidden" name="sale_order_id" value="<?= $sale_order->id ?>" />
							</div>
						</div>
						<?php } ?>
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("biller", "slbiller"); ?>
                                    <?php
                                    $bl[""] = "";
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company != '-' ? $biller->code .'-'.$biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $inv->biller_id), 'id="slbiller"  data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;pointer-events: none;"');
                                    ?>
                                </div>
                            </div>
                        <?php } else {
                            $biller_input = array(
                                'type' => 'hidden',
                                'name' => 'biller',
                                'id' => 'slbiller',
                                'value' => $this->session->userdata('biller_id'),
                            );

                            echo form_input($biller_input);
                        } ?>

                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div
                                    class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">

                                    <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) { ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= lang("warehouse", "slwarehouse"); ?>
                                                <?php
                                                $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
                                                    $wh[$warehouse->id] = $warehouse->name;
                                                }
                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $inv->warehouse_id), 'id="slwarehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                                        </div>
                                    <?php } else {
                                        $warehouse_input = array(
                                            'type' => 'hidden',
                                            'name' => 'warehouse',
                                            'id' => 'slwarehouse',
                                            'value' => $this->session->userdata('warehouse_id'),
                                        );

                                        echo form_input($warehouse_input);
                                    } ?>
									<?php if($setting->bill_to == 1) { ?>
									<div class="col-md-4">
										<div class="form-group">
											<?= lang("bill_to", "bill_to"); ?>
											<?php echo form_input('bill_to', (isset($_POST['bill_to']) ? $_POST['bill_to'] : $inv->bill_to), 'class="form-control input-tip" id="bill_to"'); ?>
										</div>
									</div>
									<?php } ?>
									<?php if($setting->show_po) { ?>
									<div class="col-md-4">
										<div class="form-group">
											<?= lang("po", "po"); ?>
											<?php echo form_input('po', (isset($_POST['po']) ? $_POST['po'] : $inv->po), 'class="form-control input-tip" id="po"'); ?>
										</div>
									</div>
									<?php } ?>
									<div class="col-md-4">
										<div class="form-group">
										<?= lang("saleman", "saleman"); ?>
										<?php
										$sm[''] = '';
										foreach($agencies as $agency){
											$sm[$agency->id] = $agency->username;
										}
										echo form_dropdown('saleman', $sm, (($inv->saleman_by != "")? $inv->saleman_by : ''), 'id="slsaleman" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("saleman") . '" style="width:100%; pointer-events:none;" ');
										?>
										</div>
                                    </div>
									<div class="col-md-4">
										<div class="form-group">
											<?= lang("group_area", "group_area"); ?>
											<?php
											 $ar[''] = '';
											foreach ($areas as $area) {
												$ar[$area->areas_g_code] = $area->areas_group;
											}
											echo form_dropdown('area', $ar, (isset($_POST['area']) ? $_POST['area'] : $inv->group_areas_id), 'id="slarea" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("group_area") . '" required="required" style="width:100%; pointer-events:none;" ');
											?>
										</div>
                                    </div>
									<div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("customer", "slcustomer"); ?>
                                            <div class="input-group">
                                                <?php
                                                    echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="slcustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                                ?>
                                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                    <a href="#" id="removeReadonly">
                                                        <i class="fa fa-unlock" id="unLock"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php
										if($this->input->get('editsales')){
											
											$q = $this->db->get_where('erp_products',array('id'=>$this->input->get('editsales')),1);
											$pcode = $q->row()->code;
											
										}
										echo form_input('add_item', (isset($pcode)?$pcode:''), 'class="form-control input-lg" id="add_item" placeholder="' . lang("add_product_to_order") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="#" id="addManually2">
                                                <i class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i>
                                            </a>
											<a href="<?= site_url('sales/edit/'.$id);?>" class="gos" ></a>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="slTable"
                                           class="table items table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
											<th><?= lang("no"); ?></th>
                                            <?php if($setting->show_code == 1 && $setting->separate_code == 1) { ?>
												<th class="col-md-2"><?= lang("product_code"); ?></th>
												<th class="col-md-4"><?= lang("product_name"); ?></th>
											<?php } ?>
                                            <?php if($setting->show_code == 1 && $setting->separate_code == 0) { ?>
												<th class="col-md-4"><?= lang("product_name") . " (" . lang("product_code") . ")"; ?></th>
											<?php } ?>
											<?php if($setting->show_code == 0) { ?>
												<th class="col-md-4"><?= lang("product_name"); ?></th>
											<?php } ?>
                                            <?php
                                            if ($Settings->product_serial) {
                                                echo '<th class="col-md-2">' . lang("serial_no") . '</th>';
                                            }
                                            ?>
                                            <?php if ($Owner || $Admin || $GP['sales-price']) { ?>
                                                <th class="col-md-1"><?= lang("unit_price"); ?></th>
                                            <?php } ?>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
                                            <th class="col-md-1"><?= lang("qoh"); ?></th>
                                            <?php
                                            if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount') || $inv->product_discount)) {
                                                echo '<th class="col-md-1">' . lang("discount") . '</th>';
                                            }
                                            ?>
                                            <?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-1">' . lang("product_tax") . '</th>';
                                            }
                                            ?>
                                            <th><?= lang("subtotal"); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                            <th style="width: 30px !important; text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php if (($Owner || $Admin || $this->session->userdata('allow_discount')) || $inv->order_discount_id) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("order_discount", "sldiscount"); ?>
                                <?php echo form_input('order_discount', '', 'class="form-control input-tip" id="sldiscount" '.(($Owner || $Admin || $this->session->userdata('allow_discount')) ? '' : 'readonly="true"')); ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("shipping", "slshipping"); ?>
                                <?php echo form_input('shipping', '', 'class="form-control input-tip" id="slshipping"'); ?>

                            </div>
                        </div>
						<?php if ($Settings->tax2) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("order_tax", "sltax2"); ?>
                                    <?php
                                    $tr[""] = "";
                                    foreach ($tax_rates as $tax) {
                                        $tr[$tax->id] = $tax->name;
                                    }
                                    echo form_dropdown('order_tax', $tr, (isset($_POST['order_tax']) ? $_POST['order_tax'] : $Settings->default_tax_rate2), 'id="sltax2" data-placeholder="' . lang("select") . ' ' . lang("order_tax") . '" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        <?php } ?>
						<!--
						<div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("delivery_by", "delivery_by"); ?>
                                <select name="delivery_by" id="delivery_by" class="form-control delivery_by">
                                    <?php 
                                        foreach($agencies as $agency){
                                            if($delivery->delivery_by == $agency->id){
                                                echo '<option value="'. $delivery->delivery_by .'" selected>'. $agency->username .'</option>';
                                            }else{
                                                echo '<option value="'. $agency->id .'">'. $agency->username .'</option>';
                                            }
                                        }
                                    ?>
                                </select>
								<input type="hidden" name="delivery_id" id="delivery_id" class="deliery_id" value="<?= $delivery->id; ?>" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_term", "slpayment_term"); ?>
                                <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="slpayment_term"'); ?>

                            </div>
                        </div>-->
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("sale_status", "slsale_status"); ?>
                                <?php $sst = array('pending' => lang('pending'), 'completed' => lang('completed'));
                                echo form_dropdown('sale_status', $sst, '', 'class="form-control input-tip" required="required" id="slsale_status"');
                                ?>

                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_term", "slpayment_term"); ?>
								<?php
                                    $ptr[""] = "";
                                    foreach ($payment_term as $term) {
                                        $ptr[$term->id] = $term->description;
                                    }
									echo form_dropdown('payment_term', $ptr,$inv->payment_term?$inv->payment_term:"", 'id="slpayment_term" data-placeholder="' . lang("payment_term_tip") .  '" class="form-control input-tip select" style="width:100%;"'); ?>
                            </div>
                        </div>

                        <!--
						<div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_status", "slpayment_status"); ?>
                                <?php $pst = array('due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid'));
                                echo form_dropdown('payment_status', $pst, '', 'class="form-control input-tip" required="required" id="slpayment_status"');
                                ?>
                            </div>
                        </div>
						-->
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-4">
									<div class="form-group">
										<?= lang("document", "document") ?>
										<input id="document" type="file" name="document" data-show-upload="false" data-show-preview="false" class="form-control file">
									</div>
								</div>
								
								<div class="col-md-4">
									<div class="form-group">
										<?= lang("document", "document") ?>
										<input id="document1" type="file" name="document1" data-show-upload="false" data-show-preview="false" class="form-control file">
									</div>
								</div>
								
								<div class="col-md-4">
									<div class="form-group">
										<?= lang("document", "document") ?>
										<input id="document2" type="file" name="document2" data-show-upload="false" data-show-preview="false" class="form-control file">
									</div>
								</div>
							</div>
						</div>
                        <div class="clearfix"></div>
						
						<div id="payments" style="display: none;">
                            <div class="col-md-12">
                                <div class="well well-sm well_1">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4" id="pay_ref">
                                                <div class="form-group">
                                                    <?= lang("payment_reference_no", "payment_reference_no"); ?>
                                                    <?= form_input('payment_reference_no', isset($payment->reference_no)?$payment->reference_no:$payment_reference, 'class="form-control tip" id="payment_reference_no"'); ?>
													<?php if($payment) { ?>
													<input type="hidden" name="payment_id" value="<?= $payment->id ?>" />
													<?php } ?>
												</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="payment">
                                                    <div class="form-group ngc">
                                                        <?= lang("amount", "amount_1"); ?>
                                                        <input name="amount-paid" type="text" id="amount_1"
                                                               class="pa form-control kb-pad amount" value="<?php echo isset($payment->amount)?$payment->amount:'' ?>"/>
														<!--<input name="amount-paid" type="text" id="amount_1"
                                                               class="pa form-control kb-pad amount" value="<?php echo isset($payment->amount)?$payment->amount:'' ?>"/>-->
                                                    </div>
                                                </div>
                                            </div>
											<div class="col-sm-4">
                                                <div class="form-group">
                                                    <?= lang("paying_by", "paid_by_1"); ?>
                                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by">
														<option value="cash"><?= lang("cash"); ?></option>
														<option value="western union"><?= lang("Western_Union"); ?></option>
														<option value="bank transfer"><?= lang("Bank_Transfer"); ?></option>
														<option value="cheque"><?= lang("cheque"); ?></option>
														<option value="other"><?= lang("other"); ?></option>
														<option value="deposit"><?= lang("deposit"); ?></option>
														<option value="depreciation"><?= lang("loan"); ?></option>
														 <option value="gift_card"><?= lang("gift_card"); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
										<div class="row">
											<div class="col-sm-4" id="bank_acc">
												<div class="form-group">
													<?= lang("bank_account", "bank_account_1"); ?>
													<?php $bank = array('' => '');
													foreach($bankAccounts as $bankAcc) {
														$bank[$bankAcc->accountcode] = $bankAcc->accountcode . ' | '. $bankAcc->accountname;
													}
													echo form_dropdown('bank_account', $bank, (($payment && $payment->bank_account)? $payment->bank_account:''), 'id="bank_account_1" class="ba form-control kb-pad bank_account" required="required"');
													?>
												</div>
											</div>
										</div>
                                        <div class="clearfix"></div>
										<div class="form-group dp" style="display: block;">
											<!--
											<?= lang("customer", "customer1"); ?>
													<?php
													$customers1[] = array();
													foreach($customers as $customer){
														$customers1[$customer->id] = $customer->name;
													}
												echo form_dropdown('customer', $customers1, '' , 'class="form-control" id="customer1"');
											?>
											-->
											<?= lang("deposit_amount", "deposit_amount"); ?>
											
											<div id="dp_details"></div>
										</div>
										
                                        <div class="form-group">
                                            <?= lang('payment_note', 'payment_note_1'); ?>
                                            <textarea name="payment_note" id="payment_note_1"
                                                      class="pa form-control kb-text payment_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>


                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>
						<input type="hidden" id="exchange_rate" value="<?= $exchange_rate->rate ?>">
						<input type="hidden" id="is_edit" value="1">

                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("sale_note", "slnote"); ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="slnote" style="margin-top: 10px; height: 100px;"'); ?>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("staff_note", "slinnote"); ?>
                                        <?php echo form_textarea('staff_note', (isset($_POST['staff_note']) ? $_POST['staff_note'] : ""), 'class="form-control" id="slinnote" style="margin-top: 10px; height: 100px;"'); ?>

                                    </div>
                                </div>


                            </div>

                        </div>
                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_sale', lang("submit"), 'id="edit_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
					<input type="hidden" id="edit_id" value="<?=$id?>">
					<input type="hidden" id="warehouse_id" value="<?=$inv->warehouse_id?>">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <?php if (($Owner || $Admin || $this->session->userdata('allow_discount')) || $inv->total_discount) { ?>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
                            <?php } ?>
                            <?php if ($Settings->tax2) { ?>
                                <td><?= lang('order_tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
                            <?php } ?>
                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
                        </tr>
                    </table>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <?php if ($Settings->tax1) { ?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?= lang('product_tax') ?></label>
                            <div class="col-sm-8">
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('ptax', $tr, "", 'id="ptax" class="form-control pos-input-tip" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($Settings->product_serial) { ?>
                        <div class="form-group">
                            <label for="pserial" class="col-sm-4 control-label"><?= lang('serial_no') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pserial">
                            </div>
                        </div>
                    <?php } ?>
					<div class="form-group">
                        <label for="piece" class="col-sm-4 control-label"><?= lang('piece') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="piece">
                        </div>
                    </div>
					<div class="form-group">
                        <label for="wpiece" class="col-sm-4 control-label"><?= lang('wpiece') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="wpiece">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?= lang('product_option') ?></label>

                        <div class="col-sm-8">
                            <div id="poptions-div"></div>
                        </div>
                    </div>
					
					<div class="form-group">
                        <label for="pgroup_prices" class="col-sm-4 control-label"><?= lang('group_price') ?></label>

                        <div class="col-sm-8">
                            <div id="pgroup_prices-div"></div>
                        </div>
                    </div>
					
                    <?php if ($Settings->product_discount) { ?>
                        <div class="form-group">
                            <label for="pdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount" <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? '' : 'readonly="true"'; ?>>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="pprice" class="col-sm-4 control-label"><?= lang('unit_price') ?></label>

                        <div class="col-sm-8">
							<input type="text" class="form-control" id="pprice_show">
                            <input type="hidden" class="form-control" id="pprice">
							<input type="hidden" class="form-control" id="curr_rate">
                        </div>
                    </div>
					
					<div class="form-group">
                        <label for="pnote" class="col-sm-4 control-label"><?= lang('product_note') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control kb-pad" id="pnote">
                        </div>
                    </div>
					
					
					
					
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="net_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>
                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="mcode" class="col-sm-4 control-label"><?= lang('product_code') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mcode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mname" class="col-sm-4 control-label"><?= lang('product_name') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mname">
                        </div>
                    </div>
                    <?php if ($Settings->tax1) { ?>
                        <div class="form-group">
                            <label for="mtax" class="col-sm-4 control-label"><?= lang('product_tax') ?> *</label>

                            <div class="col-sm-8">
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?= lang('quantity') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_serial) { ?>
                        <div class="form-group">
                            <label for="mserial" class="col-sm-4 control-label"><?= lang('product_serial') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mserial">
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($Settings->product_discount) { ?>
                        <div class="form-group">
                            <label for="mdiscount" class="col-sm-4 control-label">
                                <?= lang('product_discount') ?>
                            </label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mdiscount" <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? '' : 'readonly="true"'; ?>>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="mprice" class="col-sm-4 control-label"><?= lang('unit_price') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mprice">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="mnet_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="mpro_tax"></span></th>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mModal4" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_standard_product') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="mError"></span>
                </div>
                <div class="row">
				<div class="col-lg-12">

					<p class="introtext"><?php echo lang('enter_info'); ?></p>

					<?php
					$attrib = array('data-toggle' => 'validator', 'role' => 'form');
					
					echo form_open_multipart("products/add?salee=".$id, $attrib)
					?>

					<div class="col-md-5">
						<div class="form-group">
							<?= lang("product_type", "type") ?>
							<?php
							$opts = array('standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service'));
							echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($product ? $product->type : '')), 'class="form-control" id="type" required="required"');
							?>
						</div>
						<div class="form-group all">
							<?= lang("product_name", "name") ?>
							<?= form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($product ? $product->name : '')), 'class="form-control" id="name" required="required"'); ?>
						</div>
						<div class="form-group all">
							<?= lang("product_code", "code") ?>
							<?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code"  required="required"') ?>
							<span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>
						</div>
						<div class="form-group all">
							<?= lang("barcode_symbology", "barcode_symbology") ?>
							<?php
							$bs = array('code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca ' => 'UPC-A', 'upce' => 'UPC-E');
							echo form_dropdown('barcode_symbology', $bs, (isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : ($product ? $product->barcode_symbology : 'code128')), 'class="form-control select" id="barcode_symbology" required="required" style="width:100%;"');
							?>

						</div>
						  <div class="form-group">
							<?= lang("category", "category") ?>
							<?php if ($Owner || $Admin) { ?><div class="input-group"><?php } ?>
									<?php
									if ($Owner || $Admin ) { 
									$cat[''] = "";
									foreach ($categories as $category) {
										$cat[$category->id] = $category->name;
									}
									echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang("select") . " " . lang("category") . '" style="width:100%" required="required"')
									?>	
									<div class="input-group-addon no-print" style="padding: 2px 5px;"><a
											href="<?= site_url('system_settings/add_category'); ?>" id="add_category"
											class="external" data-toggle="modal" data-target="#myModal"><i
												class="fa fa-2x fa-plus-circle" id="addIcon"></i></a></div>
								</div>
								<?php }else{
									$cat[''] = "";
								foreach ($categories as $category) {
									$cat[$category->id] = $category->name;
								}
								echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang("select") . " " . lang("category") . '" style="width:100%" required="required"')
								?>
							<?php
							} 
							?>
						</div>
						
						 <div class="form-group all">
							<?= lang("subcategory", "subcategory") ?>
							<?php if ($Owner || $Admin) { ?><div class="input-group"><?php } ?>
								<?php
								if ($Owner || $Admin ) { 
									echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory"  placeholder="' . lang("select_category_to_load") . '"');
								?>
							
								<div class="input-group-addon no-print" style="padding: 2px 5px;"><a
										href="<?= site_url('system_settings/add_subcategory'); ?>" id="add_subcategory"
										class="external" data-toggle="modal" data-target="#myModal"><i
											class="fa fa-2x fa-plus-circle" id="addIcon"></i></a></div>
							</div>
							<?php }else{
								echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory"  placeholder="' . lang("select_category_to_load") . '"');
							} ?>
						</div>
						<div class="form-group all">
							<label class="control-label" for="unit"><?= lang("product_unit") ?></label>
						   <div class="input-group"> <?php
							$ut[""] = "";
							foreach($unit as $uts){
								$ut[$uts->id] = $uts->name;
							}
							echo form_dropdown('unit', $ut, (isset($_POST['unit']) ? $_POST['unit'] : ($product ? $product->unit : '')), 'class="form-control select" id="unit" required="required" placeholder="'.lang('select_units').'" style="width:100%;"');
							
							?>
									   <div class="input-group-addon no-print" style="padding: 2px 5px;"><a
										href="<?= site_url('system_settings/add_unit'); ?>" id="add_unit"
										class="external" data-toggle="modal" data-target="#myModal"><i
											class="fa fa-2x fa-plus-circle" id="addIcon"></i></a></div>
							</div>
							
						</div>
						<div class="form-group standard">
							<?= lang("product_cost", "cost") ?>
							<?= form_input('cost', (isset($_POST['cost']) ? $_POST['cost'] : ($product ? $this->erp->formatPurDecimal($product->cost) : '')), 'class="form-control tip" id="cost" required="required"') ?>
						</div>
						<div class="form-group all">
							<?= lang("product_price", "price") ?>
							<?= form_input('price', (isset($_POST['price']) ? $_POST['price'] : ($product ? $this->erp->formatPurDecimal($product->price) : '')), 'class="form-control tip" id="price" required="required"') ?>
						</div>

						<?php if ($Settings->tax1) { ?>
							<div class="form-group all">
								<?= lang("product_tax", "tax_rate") ?>
								<?php
								$tr[""] = "";
								foreach ($tax_rates as $tax) {
									$tr[$tax->id] = $tax->name;
								}
								echo form_dropdown('tax_rate', $tr, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : ($product ? $product->tax_rate : $Settings->default_tax_rate)), 'class="form-control select" id="tax_rate" placeholder="' . lang("select") . ' ' . lang("product_tax") . '" style="width:100%"')
								?>
							</div>
							<div class="form-group all">
								<?= lang("tax_method", "tax_method") ?>
								<?php
								$tm = array('0' => lang('inclusive'), '1' => lang('exclusive'));
								echo form_dropdown('tax_method', $tm, (isset($_POST['tax_method']) ? $_POST['tax_method'] : ($product ? $product->tax_method : '')), 'class="form-control select" id="tax_method" placeholder="' . lang("select") . ' ' . lang("tax_method") . '" style="width:100%"')
								?>
							</div>
						<?php } ?>
						<div class="form-group standard">
							<?= lang("alert_quantity", "alert_quantity") ?>
							<div
								class="input-group"> <?= form_input('alert_quantity', (isset($_POST['alert_quantity']) ? $_POST['alert_quantity'] : ($product ? $this->erp->formatQuantity($product->alert_quantity) : '')), 'class="form-control tip" id="alert_quantity"') ?>
								<span class="input-group-addon">
								<input type="checkbox" name="track_quantity" id="track_quantity"
									   value="1" <?= ($product ? (isset($product->track_quantity) ? 'checked="checked"' : '') : 'checked="checked"') ?>>
							</span>
							</div>
						</div>
				

						<div class="form-group all">
							<?= lang("product_image", "product_image") ?>
							<input id="product_image" type="file" name="product_image" data-show-upload="false"
								   data-show-preview="false" accept="image/*" class="form-control file">
						</div>

						<div class="form-group all">
							<?= lang("product_gallery_images", "images") ?>
							<input id="images" type="file" name="userfile[]" multiple="true" data-show-upload="false"
								   data-show-preview="false" class="form-control file" accept="image/*">
						</div>
						<div id="img-details"></div>
					</div>
					<div class="col-md-6 col-md-offset-1">
						<div class="standard">
							<div class="<?= $product ? 'text-warning' : '' ?>">
								<strong><?= lang("warehouse_quantity") ?></strong><br>
								<?php
								if (!empty($warehouses)) {
									if ($product) {
										echo '<div class="row"><div class="col-md-12"><div class="well"><div id="show_wh_edit">';
										if (!empty($warehouses_products)) {
											echo '<div style="display:none;">';
											foreach ($warehouses_products as $wh_pr) {
												echo '<span class="bold text-info">' . $wh_pr->name . ': <span class="padding05" id="rwh_qty_' . $wh_pr->id . '">' . $this->erp->formatQuantity($wh_pr->quantity) . '</span>' . ($wh_pr->rack ? ' (<span class="padding05" id="rrack_' . $wh_pr->id . '">' . $wh_pr->rack . '</span>)' : '') . '</span><br>';
											}
											echo '</div>';
										}
										foreach ($warehouses as $warehouse) {
											//$whs[$warehouse->id] = $warehouse->name;
											echo '<div class="col-md-6 col-sm-6 col-xs-6" style="padding-bottom:15px;">' . $warehouse->name . '<br><div class="form-group">' . form_hidden('wh_' . $warehouse->id, $warehouse->id) . form_input('wh_qty_' . $warehouse->id, (isset($_POST['wh_qty_' . $warehouse->id]) ? $_POST['wh_qty_' . $warehouse->id] : (isset($warehouse->quantity) ? $warehouse->quantity : '')), 'class="form-control wh" id="wh_qty_' . $warehouse->id . '" placeholder="' . lang('quantity') . '"') . '</div>';
											if ($this->Settings->racks) {
												echo '<div class="form-group">' . form_input('rack_' . $warehouse->id, (isset($_POST['rack_' . $warehouse->id]) ? $_POST['rack_' . $warehouse->id] : (isset($warehouse->rack) ? $warehouse->rack : '')), 'class="form-control wh" id="rack_' . $warehouse->id . '" placeholder="' . lang('rack') . '"') . '</div>';
											}
											echo '</div>';
										}
										echo '</div><div class="clearfix"></div></div></div></div>';
									} else {
										echo '<div class="row"><div class="col-md-12"><div class="well">';
										foreach ($warehouses as $warehouse) {
											//$whs[$warehouse->id] = $warehouse->name;
											echo '<div class="col-md-6 col-sm-6 col-xs-6" style="padding-bottom:15px;">' . $warehouse->name . '<br><div class="form-group">' . form_hidden('wh_' . $warehouse->id, $warehouse->id) . form_input('wh_qty_' . $warehouse->id, (isset($_POST['wh_qty_' . $warehouse->id]) ? $_POST['wh_qty_' . $warehouse->id] : ''), 'class="form-control" id="wh_qty_' . $warehouse->id . '" placeholder="' . lang('quantity') . '"') . '</div>';
											if ($this->Settings->racks) {
												echo '<div class="form-group">' . form_input('rack_' . $warehouse->id, (isset($_POST['rack_' . $warehouse->id]) ? $_POST['rack_' . $warehouse->id] : ''), 'class="form-control" id="rack_' . $warehouse->id . '" placeholder="' . lang('rack') . '"') . '</div>';
											}
											echo '</div>';
										}
										echo '<div class="clearfix"></div></div></div></div>';
									}
								}
								?>
							</div>
							<div class="clearfix"></div>
							<div id="attrs"></div>
							<div class="form-group">
								<input type="checkbox" class="checkbox" name="attributes"
									   id="attributes" <?= $this->input->post('attributes') || $product_options ? 'checked="checked"' : ''; ?>><label
									for="attributes"
									class="padding05"><?= lang('product_has_attributes'); ?></label> <?= lang('eg_sizes_colors'); ?>
							</div>
							<div class="well well-sm" id="attr-con"
								 style="<?= $this->input->post('attributes') || $product_options ? '' : 'display:none;'; ?>">
								<div class="form-group" id="ui" style="margin-bottom: 0;">
									<div class="input-group">
										<?php echo form_input('attributesInput', '', 'class="form-control select-tags" id="attributesInput" placeholder="' . $this->lang->line("enter_attributes") . '"'); ?>
										<div class="input-group-addon" style="padding: 2px 5px;"><a href="#"
																									id="addAttributes"><i
													class="fa fa-2x fa-plus-circle" id="addIcon"></i></a></div>
									</div>
									<div style="clear:both;"></div>
								</div>
								<div class="table-responsive">
									<table id="attrTable" class="table table-bordered table-condensed table-striped"
										   style="<?= $this->input->post('attributes') || $product_options ? '' : 'display:none;'; ?>margin-bottom: 0; margin-top: 10px;">
										<thead>
										<tr class="active">
											<th><?= lang('name') ?></th>
											<!--<th><?= lang('warehouse') ?></th>-->
											<th><?= lang('quantity_unit') ?></th>
											<!--<th><?= lang('quantity') ?></th>
											<th><?= lang('cost') ?></th>-->
											<th><?= lang('price') ?></th>
											<th><i class="fa fa-times attr-remove-all"></i></th>
										</tr>
										</thead>
										<tbody><?php
										if ($this->input->post('attributes')) {
											$a = sizeof($_POST['attr_name']);
											for ($r = 0; $r <= $a; $r++) {
												if (isset($_POST['attr_name'][$r]) && (isset($_POST['attr_warehouse'][$r]) || isset($_POST['attr_quantity_unit'][$r]) || isset($_POST['attr_quantity'][$r]))) {
													echo '<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' . $_POST['attr_name'][$r] . '"><span>' . $_POST['attr_name'][$r] . '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $_POST['attr_warehouse'][$r] . '"><input type="hidden" name="attr_wh_name[]" value="' . $_POST['attr_wh_name'][$r] . '"><span>' . $_POST['attr_wh_name'][$r] . '</span></td><td class="quantity_unit text-center"><input type="hidden" name="attr_quantity_unit[]" value="' . $_POST['attr_quantity_unit'][$r] . '"><span>' . $_POST['attr_quantity_unit'][$r] . '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $_POST['attr_warehouse'][$r] . '"><input type="hidden" name="attr_wh_name[]" value="' . $_POST['attr_wh_name'][$r] . '"><span>' . $_POST['attr_wh_name'][$r] . '</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="' . $_POST['attr_quantity'][$r] . '"><span>' . $_POST['attr_quantity'][$r] . '</span></td><td class="cost text-right"><input type="hidden" name="attr_cost[]" value="' . $_POST['attr_cost'][$r] . '"><span>' . $_POST['attr_cost'][$r] . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $_POST['attr_price'][$r] . '"><span>' . $_POST['attr_price'][$r] . '</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>';
												}
											}
										} elseif ($product_options) {
											foreach ($product_options as $option) {
												echo '<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' . $option->name . '"><span>' . $option->name . '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $option->warehouse_id . '"><input type="hidden" name="attr_wh_name[]" value="' . $option->wh_name . '"><span>' . $option->wh_name . '</span></td><td class="quantity_unit text-center"><input type="hidden" name="attr_quantity_unit[]" value="' . $this->erp->formatQuantity($option->wh_qty) . '"><span>' . $this->erp->formatQuantity($option->wh_qty) . '</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="' . $this->erp->formatQuantity($option->wh_qty) . '"><span>' . $this->erp->formatQuantity($option->wh_qty) . '</span></td><td class="cost text-right"><input type="hidden" name="attr_cost[]" value="' . $this->erp->formatMoneyPurchase($option->cost) . '"><span>' . $this->erp->formatMoneyPurchase($option->cost) . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $this->erp->formatMoneyPurchase($option->price) . '"><span>' . $this->erp->formatMoneyPurchase($option->price) . '</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>';
											}
										}
										?></tbody>
									</table>
								</div>
							</div>

						</div>
						<div class="combo" style="display:none;">

							<div class="form-group">
								<?= lang("add_product", "add_item") . ' (' . lang('not_with_variants') . ')'; ?>
								<?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line("add_item") . '"'); ?>
							</div>
							<div class="control-group table-group">
								<label class="table-label" for="combo"><?= lang("combo_products"); ?></label>

								<div class="controls table-controls">
									<table id="prTable"
										   class="table items table-striped table-bordered table-condensed table-hover">
										<thead>
										<tr>
											<th class="col-md-5 col-sm-5 col-xs-5"><?= lang("product_name") . " (" . $this->lang->line("product_code") . ")"; ?></th>
											<th class="col-md-2 col-sm-2 col-xs-2"><?= lang("quantity"); ?></th>
											<th class="col-md-3 col-sm-3 col-xs-3"><?= lang("unit_price"); ?></th>
											<th class="col-md-1 col-sm-1 col-xs-1 text-center"><i class="fa fa-trash-o"
																								  style="opacity:0.5; filter:alpha(opacity=50);"></i>
											</th>
										</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
							</div>

						</div>

						<div class="digital" style="display:none;">
							<div class="form-group digital">
								<?= lang("digital_file", "digital_file") ?>
								<input id="digital_file" type="file" name="digital_file" data-show-upload="false"
									   data-show-preview="false" class="form-control file">
							</div>
						</div>

					</div>

					<div class="col-md-12">

						<div class="form-group">
							<input name="cf" type="checkbox" class="checkbox" id="extras" value="" <?= isset($_POST['cf']) ? 'checked="checked"' : '' ?>/><label for="extras" class="padding05"><?= lang('custom_fields') ?></label>
						</div>
						<div class="row" id="extras-con" style="display: none;">

							<div class="col-md-4">
								<div class="form-group all">
									<?= lang('pcf1', 'cf1') ?>
									<?= form_input('cf1', (isset($_POST['cf1']) ? $_POST['cf1'] : ($product ? $product->cf1 : '')), 'class="form-control tip" id="cf1"') ?>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group all">
									<?= lang('pcf2', 'cf2') ?>
									<?= form_input('cf2', (isset($_POST['cf2']) ? $_POST['cf2'] : ($product ? $product->cf2 : '')), 'class="form-control tip" id="cf2"') ?>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group all">
									<?= lang('pcf3', 'cf3') ?>
									<?= form_input('cf3', (isset($_POST['cf3']) ? $_POST['cf3'] : ($product ? $product->cf3 : '')), 'class="form-control tip" id="cf3"') ?>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group all">
									<?= lang('pcf4', 'cf4') ?>
									<?= form_input('cf4', (isset($_POST['cf4']) ? $_POST['cf4'] : ($product ? $product->cf4 : '')), 'class="form-control tip" id="cf4"') ?>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group all">
									<?= lang('pcf5', 'cf5') ?>
									<?= form_input('cf5', (isset($_POST['cf5']) ? $_POST['cf5'] : ($product ? $product->cf5 : '')), 'class="form-control tip" id="cf5"') ?>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group all">
									<?= lang('pcf6', 'cf6') ?>
									<?= form_input('cf6', (isset($_POST['cf6']) ? $_POST['cf6'] : ($product ? $product->cf6 : '')), 'class="form-control tip" id="cf6"') ?>
								</div>
							</div>

						</div>

						<div class="form-group all">
							<?= lang("product_details", "product_details") ?>
							<?= form_textarea('product_details', (isset($_POST['product_details']) ? $_POST['product_details'] : ($product ? $product->product_details : '')), 'class="form-control" id="details"'); ?>
						</div>
						<div class="form-group all">
							<?= lang("product_details_for_invoice", "details") ?>
							<?= form_textarea('details', (isset($_POST['details']) ? $_POST['details'] : ($product ? $product->details : '')), 'class="form-control" id="details"'); ?>
						</div>

						<div class="form-group">
							<?php echo form_submit('add_product', $this->lang->line("add_product"), 'class="btn btn-primary"'); ?>
						</div>

					</div>
					<?= form_close(); ?>

				</div>
				</div>
			</div>
        </div>
    </div>
</div>


<script type="text/javascript">
	
	var $biller = $("#slbiller");
	$(window).load(function(){ 
	$('#paid_by').trigger('change');
	$('#slpayment_status').trigger('change');
	$('#amount_1').trigger('keyup');
	<?php if($Admin || $Owner){ ?>
		billerChange();
	<?php } ?>
	});
	
	function billerChange(){
        var id = $biller.val();
        //$("#slwarehouse").empty();
        $.ajax({
            url: '<?= base_url() ?>auth/getWarehouseByProject/'+id,
            dataType: 'json',
            success: function(result){
                $.each(result, function(i,val){
                    var b_id = val.id;
					var code = val.code;
                    var name = val.name;
                    var opt = '<option value="' + b_id + '">' +code+'-'+ name + '</option>';
                    $("#slwarehouse").append(opt);
					
                });
				
                $('#slwarehouse option[selected="selected"]').each(
                    function() {
                        //$(this).removeAttr('selected');
                    }
                );
				//$('#slwarehouse').val($('#slwarehouse option:first-child').val()).trigger('change');
                //$("#slwarehouse").select2("val", "<?=$Settings->default_warehouse;?>");
				
				if(slwarehouse = localStorage.getItem('slwarehouse')){
					$('#slwarehouse').select2("val", slwarehouse);
				}else{
					$("#slwarehouse").select2("val", "<?=$Settings->default_warehouse;?>");
				}
            }
        });
    }

   $(document).ready(function () {
	   
	   $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
		$('#category').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= site_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        }else{
							$("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: 'not found'
                            });
						}
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });
		
	   
	   
        $("#slcustomer").select2("destroy").empty().attr("placeholder", "<?= lang('select_customer_to_load') ?>").select2({
            placeholder: "<?= lang('select_area_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_area_to_load') ?>'}
            ]
        });
				
        $('#slarea').change(function () {
           var v = $(this).val();
            $('#modal-loading').show();			
            if (v) {				
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= site_url('sales/getCustomersByArea') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
							
                            $("#slcustomer").select2("destroy").empty().attr("placeholder", "<?= lang('select_customer') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        }else{
							
							$("#slcustomer").select2("destroy").empty().attr("placeholder", "<?= lang('select_customer') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: 'not found'
                            });
						}
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#slcustomer").select2("destroy").empty().attr("placeholder", "<?= lang('select_area_to_load') ?>").select2({
                    placeholder: "<?= lang('select_area_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_area_to_load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });       
		 
    });
	
	$(window).load(function(){
		var al = '<?php echo $this->input->get('editsales');?>';
		if(al){
			var test = $("#add_item").val();
				$.ajax({
					type: 'get',
					url: '<?= site_url('sales/suggestionsSale'); ?>',
					dataType: "json",
					data: {
						term: test,
						warehouse_id: $("#slwarehouse").val(),
						customer_id: $("#slcustomer").val()
					},
					success: function (data) {
						  for(var i = 0; i < data.length; i++){
							comment = data[i];
							add_invoice_item(comment)
						  }
						 $("#add_item").val('');	
						
					}
				});   
				
			//var url = $(".gos").attr('href');
				//		window.location.href = url;
		}
    });
	
	
</script>