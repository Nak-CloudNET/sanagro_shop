<script type="text/javascript">
    var count = 1, an = 1, po_edit = true, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?=$default_currency->code?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, poitems = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(window).bind("load", function() {
        <?= ($inv->status == 'received' || $inv->status == 'partial') ? '$(".rec_con").show();' : '$(".rec_con").hide();'; ?>
    });
    $(document).ready(function () {
		
		$('body').on('click', '#add_pruchase_test', function(e) {
			e.preventDefault();
			var deposit_balance = parseFloat($(".deposit_total_balance").text());
			var actual_total_balance = parseFloat($(".actual_total_balance").text());
			var pay_s = $("#slpayment_status").val();
			if(pay_s == "paid" || pay_s == "partial"){
				if(deposit_balance<=0){
					bootbox.alert('Not allow save: Balance can not less than 0');
					return false;
				}
				var am1= $("#amount_1").val()-0;
				if(am1<=0){
					bootbox.alert('Total amount can not less than 0.');
					return false;
				}
				if(am1>actual_total_balance){
					bootbox.alert('Not allow save: deposit '+am1+' > Actual balance '+actual_total_balance);
					return false;
				}
				
			}
			
			$('#edit_pruchase').trigger('click');
		});
		
        <?= ($inv->status == 'received' || $inv->status == 'partial') ? '$(".rec_con").show();' : '$(".rec_con").hide();'; ?>
        $('#postatus').change(function(){
            var st = $(this).val();
            if (st == 'received' || st == 'partial') {
                $(".rec_con").show();
            } else {
                $(".rec_con").hide();
            }
        });

        <?php if ($inv) { ?>
        localStorage.setItem('podate', '<?= date($dateFormats['php_ldate'], strtotime($inv->date))?>');
        localStorage.setItem('posupplier', '<?=$inv->supplier_id?>');
        localStorage.setItem('poref', '<?=$inv->reference_no?>');
		localStorage.setItem('edit_status', '<?=$edit_status?>');
        localStorage.setItem('powarehouse', '<?=$inv->warehouse_id?>');
        localStorage.setItem('postatus', '<?=$inv->status?>');
		localStorage.setItem('pur_ref', '<?=$inv->purchase_ref?>');
        localStorage.setItem('ponote', '<?= str_replace(array("\r", "\n"), "", $this->erp->decode_html($inv->note)); ?>');
        localStorage.setItem('podiscount', '<?=$inv->order_discount_id?>');
        localStorage.setItem('potax2', '<?=$inv->order_tax_id?>');
        localStorage.setItem('poshipping', '<?=$inv->shipping?>');
        localStorage.setItem('popayment_term', '<?=$inv->payment_term?>');
        localStorage.setItem('slpayment_status', '<?=$inv->payment_status?>');
        if (parseFloat(localStorage.getItem('potax2')) >= 1 || localStorage.getItem('podiscount').length >= 1 || parseFloat(localStorage.getItem('poshipping')) >= 1) {
            localStorage.setItem('poextras', '1');
        }
        //localStorage.setItem('posupplier', '<?=$inv->supplier_id?>');
        localStorage.setItem('poitems', JSON.stringify(<?=$inv_items;?>));
        <?php } ?>

        <?php if ($Owner || $Admin) { ?>
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('podate', $(this).val());
        });
        if (podate = localStorage.getItem('podate')) {
            $('#podate').val(podate);
        }
        <?php } ?>
        ItemnTotals();
        $("#add_item").autocomplete({
            source: '<?= site_url('purchases/suggestions'); ?>',
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
                    $(this).val('');
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
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_purchase_item(ui.item);
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
		$(window).load(function(){
			$('#slbiller').select2("readonly", true);
		});
           
        $(document).on('click', '#addItemManually', function (e) {
            if (!$('#mcode').val()) {
                $('#mError').text('<?=lang('product_code_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mname').val()) {
                $('#mError').text('<?=lang('product_name_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcategory').val()) {
                $('#mError').text('<?=lang('product_category_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#munit').val()) {
                $('#mError').text('<?=lang('product_unit_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcost').val()) {
                $('#mError').text('<?=lang('product_cost_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mprice').val()) {
                $('#mError').text('<?=lang('product_price_is_required')?>');
                $('#mError-con').show();
                return false;
            }

            var msg, row = null, product = {
                type: 'standard',
                code: $('#mcode').val(),
                name: $('#mname').val(),
                tax_rate: $('#mtax').val(),
                tax_method: $('#mtax_method').val(),
                category_id: $('#mcategory').val(),
                unit: $('#munit').val(),
                cost: $('#mcost').val(),
                price: $('#mprice').val()
            };

            $.ajax({
                type: "get", async: false,
                url: site.base_url + "products/addByAjax",
                data: {token: "<?= $csrf; ?>", product: product},
                dataType: "json",
                success: function (data) {
                    if (data.msg == 'success') {
                        row = add_purchase_item(data.result);
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#mModal').modal('hide');
                //audio_success.play();
            } else {
                $('#mError').text(msg);
                $('#mError-con').show();
            }
            return false;

        });
        $(window).bind('beforeunload', function (e) {
            $.get('<?=site_url('welcome/set_data/remove_pols/1');?>');
            if (count > 1) {
                var message = "You will loss data!";
                return message;
            }
        });
		if (payment_status = localStorage.getItem('slpayment_status')) {
            $('#slpayment_status').val(payment_status);
			if (payment_status == 'partial' || payment_status == 'paid') {
				$('#paid_by_1').val('deposit');
			}
			$('#payments').css('display','block');
        }
        $('#reset').click(function (e) {
            $(window).unbind('beforeunload');
        });
        $('#edit_pruchase').click(function () {
            $(window).unbind('beforeunload');
            $('form.edit-po-form').submit();
        });

    });


</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= lang('edit_purchase_order'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-po-form');
                echo form_open_multipart("purchases/edit_purchase_order/" . $inv->id, $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12">

                        <?php if ($Owner || $Admin) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("date", "podate"); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->erp->hrld($purchase->date)), 'class="form-control input-tip datetime" id="podate" required="required"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "poref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $purchase->reference_no), 'class="form-control input-tip" id="poref" required="required" readonly'); ?>
                            </div>
                        </div>
						
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("project", "slbiller"); ?>
                                    <?php
                                    $bl[""] = "";
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company != '-' ?$biller->code .'-'. $biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl,(isset($_POST['biller']) ? $_POST['biller'] : $purchase->biller_id), 'id="slbiller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
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
						
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("warehouse", "powarehouse"); ?>
                                <?php
                                $wh[''] = '';
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->code .'-'.$warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $purchase->warehouse_id), 'id="powarehouse" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div style="display:none" class="col-md-4">
                            <div class="form-group">
                                <?= lang("status", "postatus"); ?>
                                <?php
                                $post = array('received' => lang('received'), 'partial' => lang('partial'), 'pending' => lang('pending'), 'ordered' => lang('ordered'));
                                echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : $purchase->status), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("status") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
						<!--
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_term", "slpayment_term"); ?>
                                <?php 
									//echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="slpayment_term"'); 
									$pt[''] = '';
									foreach($payment_term as $pterm){
										$pt[$pterm->id] = $pterm->description;
									}
									echo form_dropdown('payment_term', $pt, (isset($_POST['payment_term']) ? $_POST['payment_term'] : $purchase->payment_term), 'id="slpayment_term" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("payment_term") . '" style="width:100%;" ');
								?>
							</div>
                        </div>
                        -->						
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("document", "document") ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div
                                    class="panel-heading"><?= lang('please_select_these_before_adding_product') ?>
								</div>
							
                                <div class="panel-body" style="padding: 5px;">
									<div class="col-sm-4">
										<div class="form-group">
										<?= lang("supplier", "posupplier"); ?>
										<?php 
											$sup[''] = '';
											foreach($suppliers as $supplier){
												$sup[$supplier->id] = $supplier->code .'-'. $supplier->name;
											}
											if($inv->purchase_ref!=""){
												echo form_dropdown('supplier', $sup, (isset($_POST['supplier']) ? $_POST['supplier'] : $purchase->supplier_id), 'id="posupplier" readonly class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '"  style="width:100%;" required="required" ');
											}else{
												echo form_dropdown('supplier', $sup, (isset($_POST['supplier']) ? $_POST['supplier'] : $purchase->supplier_id), 'id="posupplier" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '"  style="width:100%;" required="required" ');
											}
										?>
										<input type="hidden" name="supplier_id" value="" id="supplier_id"
                                                       class="form-control">
										</div>
									</div>
                                    <!--<div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("supplier", "posupplier"); ?>
											<?php if($inv->purchase_ref!=""){?>
											<div class="input-group">
                                                <input type="hidden" name="supplier" value="" readonly id="posupplier"
                                                       class="form-control" style="width:100%;"
                                                       placeholder="<?= lang("select") . ' ' . lang("supplier") ?>">

                                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                    <a href="#" id="removeReadonly">
                                                        <i class="fa fa-unlock" id="unLock"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <input type="hidden" name="supplier_id" value="" id="supplier_id" class="form-control">
												
											<?php }else{ ?>
                                            <div class="input-group">
                                                <input type="hidden" name="supplier" value="" id="posupplier"
                                                       class="form-control" style="width:100%;"
                                                       placeholder="<?= lang("select") . ' ' . lang("supplier") ?>">

                                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                    <a href="#" id="removeReadonly">
                                                        <i class="fa fa-unlock" id="unLock"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <input type="hidden" name="supplier_id" value="" id="supplier_id" class="form-control">
											
											<?php } ?>
                                        </div>
                                    </div>-->

                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php
										// if($this->input->get('editpurrquestorder')){
											
											$q = $this->db->get_where('erp_products',array('id'=>$this->input->get('editpurrquestorder')),1);
                                            $pcode = $q->row();
											// $pcode = $q->row()->code;
											
										// }		
										echo form_input('add_item', $pcode?$pcode:'', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_product_to_order") . '"'); ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="#" id="addManually2"><i class="fa fa-2x fa-plus-circle addIcon"
                                                                            id="addIcon"></i></a>
																			
																			</div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?></label>

                                <div class="controls table-controls">
                                    <table id="poTable"
                                           class="table items table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
											<th  class=""><?= lang("no"); ?></th>
                                            <th class="col-md-4"><?= lang("product_name") . " (" . $this->lang->line("product_code") . ")"; ?></th>
                                            <?php
                                            if ($Settings->product_expiry) {
                                                echo '<th class="col-md-1">' . $this->lang->line("expiry_date") . '</th>';
                                            }
                                            ?>
                                            <?php if ($Owner || $Admin || $GP['purchase_order-price']) { ?>
                                                <th class="col-md-1"><?= lang("price"); ?></th>
                                            <?php } ?>
                                            <?php if ($Owner || $Admin || $GP['purchase_order-cost']) { ?>
                                                <th class="col-md-1"><?= lang("unit_cost"); ?></th>
                                            <?php } ?>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
											<th class="col-md-1"><?= lang("stock_in_hand"); ?></th>
                                            <th class="col-md-1 rec_con"><?= lang("received"); ?></th>
                                            <?php
                                                if ($Settings->product_discount) {
                                                    echo '<th class="col-md-1">' . $this->lang->line("discount") . '</th>';
                                                }
                                            ?>
                                            <?php
                                                if ($Settings->tax1) {
                                                    echo '<th class="col-md-1">' . $this->lang->line("product_tax") . '</th>';
                                                }
                                            ?>
                                            <th><?= lang("subtotal"); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                            <th style="width: 30px !important; text-align: center;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" class="checkbox" id="extras" value=""/>
								<label for="extras" class="padding05"><?= lang('more_options') ?></label>
                            </div>
                            <div class="row" id="extras-con" style="display: none;">
                                
								<div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("discount_label", "podiscount"); ?>
                                        <?php echo form_input('discount', '', 'class="form-control input-tip" id="podiscount"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("shipping", "poshipping"); ?>
                                        <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
                                    </div>
                                </div>
								
								<?php if ($Settings->tax1) { ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('order_tax', 'potax2') ?>
                                            <?php
                                            $tr[""] = "";
                                            foreach ($tax_rates as $tax) {
                                                $tr[$tax->id] = $tax->name;
                                            }
                                            echo form_dropdown('order_tax', $tr, "", 'id="potax2" class="form-control input-tip select" style="width:100%;"');
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>

                               <!-- <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang("payment_status", "slpayment_status"); ?>
                                        <?php $pst = array('due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid'));
                                        echo form_dropdown('payment_status', $pst, '', 'class="form-control input-tip" id="slpayment_status"'); ?>
                                    </div>
                                </div>-->
                            </div>

                            <div class="clearfix"></div>
                            <div id="payments" style="display: none;">
                                
                                <div class="well well-sm well_1">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4" style="display:none;">
                                                <div class="form-group">
                                                    <?= lang("payment_reference_no", "payment_reference_no"); ?>
                                                    <?= form_input('payment_reference_no', (isset($_POST['payment_reference_no']) ? $_POST['payment_reference_no'] : $payment_ref), 'class="form-control tip" readonly id="payment_reference_no" required="required"'); ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="payment">
                                                    <div class="form-group ngc">
                                                        <?= lang("amount", "amount_1"); ?>
                                                        <input name="amount-paid" type="text" id="amount_1" class="pa form-control kb-pad amount" value="<?= $this->erp->formatPurDecimal($inv->paid);?>" />
														<input name="amount_o" type="hidden" value="<?=$this->erp->formatPurDecimal($inv->paid)?>" id="amount_o"/>
                                                    </div>
                                                    <div class="form-group gc" style="display: none;">
                                                        <?= lang("gift_card_no", "gift_card_no"); ?>
                                                        <input name="gift_card_no" type="text" id="gift_card_no"
                                                               class="pa form-control kb-pad"/>

                                                        <div id="gc_details"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <?= lang("paying_by", "paid_by_1"); ?>
                                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by">
														<option value="deposit"><?= lang("deposit"); ?></option>
													</select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="pcc_1" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="pcc_no" type="text" id="pcc_no_1"
                                                               class="form-control" placeholder="<?= lang('cc_no') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="pcc_holder" type="text" id="pcc_holder_1"
                                                               class="form-control"
                                                               placeholder="<?= lang('cc_holder') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <select name="pcc_type" id="pcc_type_1"
                                                                class="form-control pcc_type"
                                                                placeholder="<?= lang('card_type') ?>">
                                                            <option value="Visa"><?= lang("Visa"); ?></option>
                                                            <option
                                                                value="MasterCard"><?= lang("MasterCard"); ?></option>
                                                            <option value="Amex"><?= lang("Amex"); ?></option>
                                                            <option value="Discover"><?= lang("Discover"); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="pcc_month" type="text" id="pcc_month_1"
                                                               class="form-control" placeholder="<?= lang('month') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <input name="pcc_year" type="text" id="pcc_year_1"
                                                               class="form-control" placeholder="<?= lang('year') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <input name="pcc_ccv" type="text" id="pcc_cvv2_1"
                                                               class="form-control" placeholder="<?= lang('cvv2') ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group dp" style="display: none;">
                                            <?= lang("deposit_amount", "deposit_amount"); ?>
                                            
                                            <div id="dp_details"></div>
                                        </div>
                                        
                                        
                                        <div class="depreciation_1" style="display:none;">
                                            <div class="form-group">
                                                <?= lang("depre_term", "depreciation_1"); ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input name="depreciation_rate1" type="text" id="depreciation_rate_1"
                                                               class="form-control depreciation_rate1"
                                                               placeholder="<?= lang('rate (%)') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">

                                                        <input name="depreciation_term" type="text" id="depreciation_term_1"
                                                               class="form-control kb-pad" value=""
                                                               placeholder="<?= lang('term (month)') ?>"/>
                                                        <input type="hidden" id="current_date" class="current_date" class="current_date[]" value="<?php echo date('m/d/Y'); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="depreciation_type" id="depreciation_type_1"
                                                                class="form-control depreciation_type"
                                                                placeholder="<?= lang('payment type') ?>">
                                                            <option value=""> &nbsp; </option>
                                                            <option value="1"><?= lang("Normal"); ?></option>
                                                            <option value="2"><?= lang("Custom"); ?></option>
                                                            <option value="3"><?= lang("Fixed"); ?></option>
                                                            <option value="4"><?= lang("Normal(Fixed)"); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="print_" style="display:none">
                                                        <button type="button" class="btn btn-primary col-md-12 print_depre" id="print_depre" style="margin-bottom:5px;"><i class="fa fa-print"> &nbsp; </i>
                                                            <?= lang('Print') ?>
                                                        </button>
                                                        <button type="button" class="btn btn-primary col-md-12 export_depre" id="export_depre" style="margin-bottom:5px;"><i class="fa fa-file-excel-o"> &nbsp; </i>
                                                                <?= lang('export') ?>
                                                            </button>
                                                        <div style="clear:both; height:15px;"></div>
                                                    </div>
                                                 </div>
                                            </div>
                                            <div class="form-group">
                                                
                                                <div class="dep_tbl" style="display:none;">
                                                    <table border="1" width="100%" class="table table-bordered table-condensed tbl_dep" id="tbl_dep">
                                                        <tbody>
                                                    
                                                        </tbody>
                                                    </table>
                                                    <table id="export_tbl" width="70%" style="display:none;">
                                                        
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="pcheque_1" style="display:none;">
                                            <div class="form-group"><?= lang("cheque_no", "cheque_no_1"); ?>
                                                <input name="cheque_no" type="text" id="cheque_no_1"
                                                       class="form-control cheque_no"/>
                                            </div>
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

                            <div class="clearfix"></div>
                            <div class="form-group">
                                <?= lang("note", "ponote"); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('edit_pruchase', $this->lang->line("submit"), 'id="edit_pruchase" class="btn btn-primary" style="padding: 6px 15px;display:none; margin:15px 0;"'); ?>
								<button type="button" class="btn btn-primary" id="add_pruchase_test" style="padding: 6px 15px; margin:15px 0;"><?= lang('submit') ?></button>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
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
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
					<div class="form-group" style="display:none;">
						<label class="col-sm-4 control-label"><?= lang('suppliers') ?></label>
						<div class="col-sm-8">
						   <input type="hidden" name="psupplier[]" value="" id="psupplier"class="form-control" style="width:100%;" placeholder="<?= lang("select") . ' ' . lang("supplier") ?>">
						</div>
					</div>
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
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_expiry) { ?>
                        <div class="form-group">
                            <label for="pexpiry" class="col-sm-4 control-label"><?= lang('product_expiry') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control date" id="pexpiry">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?= lang('product_option') ?></label>

                        <div class="col-sm-8">
                            <div id="poptions-div"></div>
                        </div>
                    </div>
                    <?php if ($Settings->product_discount) { ?>
                        <div class="form-group">
                            <label for="pdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="pcost" class="col-sm-4 control-label"><?= lang('unit_cost') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pcost">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_cost'); ?></th>
                            <th style="width:25%;"><span id="net_cost"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>
                    <input type="hidden" id="punit_cost" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_cost" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mModal2" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
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
					echo form_open_multipart("products/add?editprquestorder=".$id, $attrib)
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
							<?= lang("product_cost", "cost") ?> <b>*</b>
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
							<?php echo form_submit('add_product', $this->lang->line("add_product"), 'class="btn btn-primary add_product" style="display:none;"'); ?>
							<button class="btn btn-primary add_product_auto"><?=$this->lang->line("add_product")?></button><span style="color:red;padding-left:20px;" class="request_"></span>
						</div>

					</div>
					<?= form_close(); ?>

				</div>
				</div>
			</div>
        </div>
    </div>
</div>
<script>
$(window).load(function(){	
		var al = '<?php echo $this->input->get('editpurrquestorder');?>';
		
		if(al){
			
			var test = $("#add_item").val();
				$.ajax({
					type: 'get',
					url: '<?= site_url('purchases/suggestions'); ?>',
					dataType: "json",
					data: {
						term: test,
						warehouse_id:localStorage.getItem('powarehouse'),
						supplier_id: localStorage.getItem('posupplier')
					},
					success: function (data) {
						  for(var i = 0; i < data.length; i++){
							comment = data[i];
							add_purchase_item(comment);
						  }
						 $("#add_item").val('');	
						//var url = $(".gos").attr('href');
						//window.location.href = url;
					}
				});   
		}
    });
$(document).ready(function(){
		
			$('body').on('click', '.add_product_auto', function(e) {
			e.preventDefault();
			var pname = $("#name").val();
			var code = $("#code").val();
			var category = $("#category").val();
			var unit = $("#unit").val();
			var cost = $("#cost").val();
			var price = $("#price").val();
			if(pname && code && category && unit && cost && price){
				$(".add_product").trigger("click");
			}
			$(".request_").text("Please input required fields (*)");
		});
	
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
	});
	
		
</script>