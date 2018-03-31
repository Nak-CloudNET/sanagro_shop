<?php 
	//$this->erp->print_arrays($quote_id);
?>

<script type="text/javascript">

    var count = 1, an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, shipping = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>;
    var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
    var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
    $(document).ready(function () {
		$("#reref").attr('disabled','disabled'); 
		$('#ref_st').on('ifChanged', function() {
		  if ($(this).is(':checked')) {
			$("#reref").prop('disabled', false);
			$("#reref").val("");
		  }else{
			$("#reref").prop('disabled', true);
			var temp = $("#temp_reference_no").val();
			$("#reref").val(temp);
			
		  }
		});
		var test2 =  '<?=$this->session->userdata('remove_q2');?>';
		
		if(test2 == '1'){
			 if (localStorage.getItem('quitems')) {
                        localStorage.removeItem('quitems');
                    }
                    if (localStorage.getItem('qudiscount')) {
                        localStorage.removeItem('qudiscount');
                    }
                    if (localStorage.getItem('qutax2')) {
                        localStorage.removeItem('qutax2');
                    }
                    if (localStorage.getItem('qushipping')) {
                        localStorage.removeItem('qushipping');
                    }
                    if (localStorage.getItem('quref')) {
                        localStorage.removeItem('quref');
                    }
                    if (localStorage.getItem('quwarehouse')) {
                        localStorage.removeItem('quwarehouse');
                    }
                    if (localStorage.getItem('qunote')) {
                        localStorage.removeItem('qunote');
                    }
                    if (localStorage.getItem('quinnote')) {
                        localStorage.removeItem('quinnote');
                    }
                    if (localStorage.getItem('qucustomer')) {
                        localStorage.removeItem('qucustomer');
                    }
                    if (localStorage.getItem('qucurrency')) {
                        localStorage.removeItem('qucurrency');
                    }
                    if (localStorage.getItem('qudate')) {
                       // localStorage.removeItem('qudate');
                    }
                    if (localStorage.getItem('qustatus')) {
                        localStorage.removeItem('qustatus');
                    }
                    if (localStorage.getItem('qubiller')) {
                        localStorage.removeItem('qubiller');
                    }
			<?=$this->session->set_userdata('remove_q2', '0');?>
			
		}
		
		
		
        <?php if($this->input->get('customer')) { ?>
        if (!localStorage.getItem('quitems')) {
            localStorage.setItem('qucustomer', <?=$this->input->get('customer');?>);
        }
		
        <?php } ?>
        <?php if ($Owner || $Admin) { ?>
		
        if (!localStorage.getItem('qudate')) {
            $("#qudate").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                language: 'erp',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
		
        $(document).on('change', '#qudate', function (e) {
            localStorage.setItem('qudate', $(this).val());
        });
        if (qudate = localStorage.getItem('qudate')) {
            $('#qudate').val(qudate);
        }
        $(document).on('change', '#qubiller', function (e) {
            localStorage.setItem('qubiller', $(this).val());
			billerChange();
        });
        if (qubiller = localStorage.getItem('qubiller')) {
            $('#qubiller').val(qubiller);
        }
        <?php } ?>
		
		$(document).on('change', '#slarea', function (e) {
            localStorage.setItem('group_area', $(this).val());
        });
		if (group_area = localStorage.getItem('group_area')) {
            $('#slarea').val(group_area);
        }
		
		$(document).on('change', '#slpayment_term', function (e) {
            localStorage.setItem('payment_term', $(this).val());
        });
		if (payment_term = localStorage.getItem('payment_term')) {
            $('#slpayment_term').val(payment_term);
        }
		
		if(localStorage.getItem('qucustomer')){
			$("#add-deposit").attr('href', site.base_url + 'customers/add_deposit/' + localStorage.getItem('qucustomer'));
		}
        if (!localStorage.getItem('qutax2')) {
            localStorage.setItem('qutax2', <?=$Settings->default_tax_rate2;?>);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#qucustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= site_url('quotes/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#quwarehouse").val(),
                        customer_id: $("#qucustomer").val()
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
                  //  $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
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
                            $("#qucustomer").select2("destroy").empty().attr("placeholder", "<?= lang('select_customer') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        }else{
							
							$("#qucustomer").select2("destroy").empty().attr("placeholder", "<?= lang('select_customer') ?>").select2({
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
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= site_url('sales/getCustomersByArea') ?>",
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#qucustomer").select2("destroy").empty().attr("placeholder", "<?= lang('select_customer') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        }else{
							$("#qucustomer").select2("destroy").empty().attr("placeholder", "<?= lang('select_customer') ?>").select2({
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
            }
            $('#modal-loading').hide();
        });  

    });
	
	$(document).on('change', '.paid_by', function () {
		var p_val = $(this).val(),
		id = $(this).attr('id');
		if(p_val == 'none'){
			$('.dp').hide();
		}
		if(p_val == 'deposit') {
			$('.dp').show();
			checkDeposit();
		}
	});
	
	$(document).on('change', '.paid_by', function (){
		$('#qucustomer').trigger('change.select2');
	});
	
	function checkDeposit() {
		var customer_id = $("#qucustomer").val();

		if (customer_id != '') {
			$.ajax({
				type: "get", async: false,
				url: site.base_url + "sales/validate_deposit/" + customer_id,
				dataType: "json",
				success: function (data) {
					if (data === false) {
						$('#deposit_no').parent('.form-group').addClass('has-error');
						bootbox.alert('<?=lang('invalid_customer')?>');
					} else if (data.id !== null && data.id !== customer_id) {
						$('#deposit_no').parent('.form-group').addClass('has-error');
						bootbox.alert("<?=lang('this_customer_has_no_deposit')?>");
						$('select').select2("val", 'none');
					} else {
						amount = $("#amount_1").val();
						$('#dp_details').html('<small>Customer Name: ' + data.name + '<br>Amount: <span class="deposit_total_amount">' + (data.deposit_amount == null ? 0 : formatDecimal(data.deposit_amount)) + '</span> - Balance: <span class="deposit_total_balance">' +formatDecimal(data.deposit_amount - amount) + '</span></small>');
						$('#deposit_no').parent('.form-group').removeClass('has-error');
						//calculateTotals();
						//$('#amount_1').val(data.deposit_amount - amount).focus();
					}
				}
			});
		}
	}
	$(document).on('keyup','#amount_1', function(event){
		//var total_amount = $('#quick-payable').text()-0;
		var us_paid = $('#amount_1').val()-0;

		var balance = us_paid;
		
		var deposit_amount = parseFloat($(".deposit_total_amount").text());
		var deposit_balance = parseFloat($(".deposit_total_balance").text());
		deposit_balance = (deposit_amount - Math.abs(us_paid));
		$(".deposit_total_balance").text(deposit_balance);
	}).on('keydown','#amount_1', function(event){
	   // Allow: backspace, delete, tab, escape, and enter
	   if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
		   // Allow: Ctrl+A
		   (event.keyCode == 65 && event.ctrlKey === true) ||
		   // Allow: home, end, left, right
		   (event.keyCode >= 35 && event.keyCode <= 39)) {
		   // let it happen, don't do anything
		   return;
	   } else {
		   // Ensure that it is a number and stop the keypress
		   if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
			   event.preventDefault();
		   }
	   }
   });
	
	
		
	
	
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_quote'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("quotes/add", $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($Owner || $Admin) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("date", "qudate"); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control input-tip datetime" id="qudate" required="required"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-4">
                            <?= lang("reference_no", "slref"); ?>
							<div style="float:left;width:100%;">
								<div class="form-group">
									<div class="input-group" style="width:100%">  
											<?php echo form_input('reference_no', $reference?$reference:"",'class="form-control input-tip" id="reref"'); ?>
											<input type="hidden"  name="temp_reference_no"  id="temp_reference_no" value="<?= $reference?$reference:"" ?>" />
											
											<div class="input-group-addon no-print" style="padding: 2px 5px;background-color:white;">
											<input type="checkbox" name="ref_status" id="ref_st" value="1" style="margin-top:3px;">
										</div>
									</div>
								</div>
							</div>
                        </div>
						
						<?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("biller", "qubiller"); ?>
                                    <?php
                                    $bl[""] = "";
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company != '-' ? $biller->code .'-'.$biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $Settings->default_biller), 'id="qubiller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        <?php } else if($this->session->userdata('biller_id')){ ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("biller", "qubiller"); ?>
                                    <?php
                                    $bl[""] = "";
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company != '-' ? $biller->code .'-'.$biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $this->session->userdata('biller_id')), 'id="qubiller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;pointer-events: none;"');
                                    ?>
                                </div>
                            </div>
                        <?php } ?>
						
						<div class="col-sm-4">
							<div class="form-group">
								<?= lang("saleman", "saleman"); ?>
								<?php
								$sm[''] = '';
								foreach($agencies as $agency){
									$sm[$agency->id] = $agency->emp_code .'-'.$agency->username;
								}
								echo form_dropdown('saleman', $sm, (isset($_POST['saleman']) ? $_POST['saleman'] : $this->session->userdata('user_id')), 'id="slsaleman" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("saleman") . '" style="width:100%;" ');
								?>
							</div>
						</div>
						
						<!--
						<div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_term", "slpayment_term"); ?>
								<?php
                                    $ptr[""] = "";
                                    foreach ($payment_term as $term) {
                                        $ptr[$term->id] = $term->description;
                                    }
									echo form_dropdown('payment_term', $ptr,$sale_order->payment_term?$sale_order->payment_term:"", 'id="slpayment_term" data-placeholder="' . lang("payment_term_tip") .  '" required="required" class="form-control input-tip select" style="width:100%;"');?>
                            </div>
                        </div>
						-->
                        
                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">
									<?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) { ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= lang("warehouse", "quwarehouse"); ?>
                                                <?php
                                                 $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
                                                    $wh[$warehouse->id] = $warehouse->name;
                                                }
                                                echo form_dropdown('warehouse', '', (isset($_POST['warehouse']) ? $_POST['warehouse'] : $Settings->default_warehouse), 'id="quwarehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                                        </div>
                                    <?php } else if($this->session->userdata('warehouse_id')){ ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= lang("warehouse", "quwarehouse"); ?>
                                                <?php
                                                 $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
                                                    $wh[$warehouse->id] = $warehouse->name;
                                                }
                                                echo form_dropdown('warehouse', '', (isset($_POST['warehouse']) ? $_POST['warehouse'] : $this->session->userdata('warehouse_id')), 'id="quwarehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                                        </div>
                                    <?php } ?>
									
									<div class="col-md-4">
										<div class="form-group">
											<?= lang("group_area", "group_area"); ?>
											<?php
											 $ar[''] = '';
											foreach ($areas as $area) {
												$ar[$area->areas_g_code] = $area->areas_group;
											}
											echo form_dropdown('area', $ar, (isset($_POST['area']) ? $_POST['area'] : ''), 'id="slarea" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("group_area") . '" required="required" style="width:100%;" ');
											?>
										</div>
                                    </div>
									
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("customer", "qucustomer"); ?>
                                            <?php if ($Owner || $Admin || $GP['customers-add']) { ?><div class="input-group"><?php } ?>
                                                <?php
                                                echo form_input('customer_1', (isset($_POST['customer']) ? $_POST['customer'] : (isset($sale_order->company_name)?$sale_order->company_name:$this->input->get('customer'))), 'id="qucustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="min-width:100%;"');
                                                ?>
                                                <?php if ($Owner || $Admin || $GP['customers-add']) { ?>

												<div class="input-group-addon no-print" style="padding: 2px 5px; border-left: 0;">
													<a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
														<i class="fa fa-2x fa-user" id="addIcon"></i>
													</a>
												</div>

                                                <div class="input-group-addon no-print" style="padding: 2px 5px;"><a
                                                        href="<?= site_url('customers/add/aquote'); ?>" id="add-customer"
                                                        class="external" data-toggle="modal" data-target="#myModal"><i
                                                            class="fa fa-2x fa-plus-circle" id="addIcon"></i></a></div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
									
									
									<div class="col-md-4">
										<!--
                                        <div class="form-group">
                                            <?= lang("deposit", "qudeposit"); ?>
											
                                            <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
											<div class="input-group"><?php } ?>
                                                <select name="paid_by" id="paid_by" class="form-control paid_by">
                                                    <option value="none"><?= lang("none"); ?></option>
													<option value="deposit"><?= lang("deposit"); ?></option>
                                                </select>
                                                <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 5px;"><a
                                                        href="<?= site_url('quotes/add_deposit'); ?>" id="add-deposit"
                                                        class="external" data-toggle="modal" data-target="#myModal"><i
                                                            class="fa fa-2x fa-plus-circle" id="addIcon"></i></a></div>
                                            </div>
                                            <?php } ?>
                                        </div>
										-->
										<div class="form-group dp" style="display: none;">
											<?= lang("deposit_amount", "deposit_amount"); ?>
											<div class="">
												<input type="text" name="amount" class="form-control amount_1" id="amount_1" placeholder="amount">
											</div>
											<div id="dp_details"></div>
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
											if($this->input->get('addquote')){
											
											$q = $this->db->get_where('erp_products',array('id'=>$this->input->get('addquote')),1);
											$pcode = $q->row()->code;
											
										}
										echo form_input('add_item', (isset($pcode)?$pcode:''), 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_product_to_order") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="#" id="addManually" class="tip"
                                               title="<?= lang('add_product_manually') ?>"><i
                                                    class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i></a>
													<a href="<?= site_url('quotes/add');?>" class="gos" ></a>
													</div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
						
						<input type="hidden" id="exchange_rate" value="<?= $exchange_rate->rate ?>">
						
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="quTable"
                                           class="table items table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
											<th class=""><?= lang("no"); ?></th>
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
											
                                            <th class="col-md-1"><?= lang("unit_price"); ?></th>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
											
											<?php if($Settings->shipping && $quote_id){?>
												<th class="col-md-1"><?= lang("quantity_recieve"); ?></th>
											<?php } ?>
											
                                            <th class="col-md-1"><?= lang("qoh"); ?></th>
                                            <?php
											
                                            if ($Settings->product_discount || ($Owner || $Admin || $this->session->userdata('allow_discount'))) {
                                                echo '<th class="col-md-1">' . $this->lang->line("discount") . '</th>';
                                            }
                                            ?>
                                            <?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-2">' . $this->lang->line("product_tax") . '</th>';
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
						
						<div class="col-md-12">

							<?php if ($Owner || $Admin || $this->session->userdata('allow_discount')) { ?>
							<div class="col-md-4">
								<div class="form-group">
									<?= lang("discount", "qudiscount"); ?>
									<?php echo form_input('discount', '', 'class="form-control input-tip" id="qudiscount"'); ?>
								</div>
							</div>
							<?php } ?>

							<div class="col-md-4">
								<div class="form-group">
									<?= lang("shipping", "qushipping"); ?>
									<?php echo form_input('shipping', '', 'class="form-control input-tip" id="qushipping"'); ?>

								</div>
							</div>
							<?php if ($Settings->tax2) { ?>
								<div class="col-md-4">
									<div class="form-group">
										<?= lang("order_tax", "qutax2"); ?>
										<?php
										$tr[""] = "";
										foreach ($tax_rates as $tax) {
											$tr[$tax->id] = $tax->name;
										}
										echo form_dropdown('order_tax', $tr, (isset($_POST['tax2']) ? $_POST['tax2'] : $Settings->default_tax_rate2), 'id="qutax2" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("order_tax") . '" required="required" class="form-control input-tip select" style="width:100%;"');
										?>
									</div>
								</div>
							<?php } ?>
							<div class="col-md-4" style="display:none">
								<div class="form-group">
									<?= lang("status", "qustatus"); ?>
									<?php $st = array('pending' => lang('pending'), 'sent' => lang('sent'));
									echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="qustatus"'); ?>

								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<?= lang("document", "document") ?>
									<input id="document" type="file" name="document" data-show-upload="false"
										   data-show-preview="false" class="form-control file">
								</div>
							</div>
						</div>
						
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="row" id="bt">
                            <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <?= lang("note", "qunote"); ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="qunote" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-12">
                            <div
                                class="fprom-group"><?php echo form_submit('add_quote', $this->lang->line("submit"), 'id="add_quote" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <?php if ($Owner || $Admin || $this->session->userdata('allow_discount')) { ?>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
                            <?php } ?>
                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                            <?php if ($Settings->tax2) { ?>
                                <td><?= lang('order_tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
                            <?php } ?>
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
					
					<!--
					<div class="form-group">
                        <label for="pg" class="col-sm-4 control-label"><?= lang('price_groups') ?></label>

                        <div class="col-sm-8">
                            <div id="pg-div"></div>
                        </div>
                    </div>
					-->
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) { ?>
                        <div class="form-group">
                            <label for="pdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount">
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
					echo form_open_multipart("products/add?quote=quote", $attrib)
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

<script type="text/javascript">
$(window).load(function(){
		var al = '<?php echo $this->input->get('addquote');?>';
		if(al){
			var test = $("#add_item").val();
				$.ajax({
					type: 'get',
					url: '<?= site_url('quotes/suggestions'); ?>',
					dataType: "json",
					data: {
						term: test,
						warehouse_id: localStorage.getItem('quwarehouse'),
                        customer_id:  localStorage.getItem('qucustomer')
					},
					success: function (data) {
						  for(var i = 0; i < data.length; i++){
							comment = data[i];
							add_invoice_item(comment);
						  }
						 $("#add_item").val('');	
						var url = $(".gos").attr('href');
						window.location.href = url;
					}
				});   
				
	
		}
    });
	var $biller = $("#qubiller");
		$(window).load(function(){
			billerChange();
			$('#slarea').change();
		});
		
	function billerChange(){
        var id = $biller.val();
        $("#quwarehouse").empty();
        $.ajax({
            url: '<?= base_url() ?>auth/getWarehouseByProject/'+id,
            dataType: 'json',
            success: function(result){
                $.each(result, function(i,val){
                    var b_id = val.id;
                    var name = val.name;
                    var opt = '<option value="' + b_id + '">' + name + '</option>';
                    $("#quwarehouse").append(opt);
                });
                $('#quwarehouse option[selected="selected"]').each(
                    function() {
                        //$(this).removeAttr('selected');
                    }
                );
				//$('#quwarehouse').val($('#quwarehouse option:first-child').val()).trigger('change');
                //$("#slwarehouse").select2("val", "<?=$Settings->default_warehouse;?>");
				if(quwarehouse = localStorage.getItem('quwarehouse')){
					$('#quwarehouse').select2("val", quwarehouse);
				}else{
					$("#quwarehouse").select2("val", "<?=$Settings->default_warehouse;?>");
				}
				
            }
        });
		
		$.ajax({
            url: '<?= base_url() ?>sales/getReferenceByProject/qu/'+id,
            dataType: 'json',
            success: function(data){
                $("#reref").val(data);
				$("#temp_reference_no").val(data);
				
            }
        });
		
    }
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