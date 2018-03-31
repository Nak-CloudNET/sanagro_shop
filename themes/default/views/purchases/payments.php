<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('view_payments'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
			
                <table id="CompTable" class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line("date"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("reference_no"); ?></th>
                        <th style="width:15%;"><?= $this->lang->line("amount"); ?></th>
                        <th style="width:15%;"><?= $this->lang->line("paid_by"); ?></th>
                        <th style="width:10%;"><?= $this->lang->line("actions"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($payments)) {
						$total = 0;
                        foreach ($payments as $payment) { ?>
                            <tr class="row<?= $payment->id ?>">
                                <td><?= $this->erp->hrld($payment->date); ?></td>
                                <td><?= lang($payment->reference_no); ?></td>
                                <td><?= $this->erp->formatMoney($payment->amount) . ' ' . (($payment->attachment) ? '<a href="' . base_url('assets/uploads/' . $payment->attachment) . '" target="_blank"><i class="fa fa-chain"></i></a>' : ''); ?></td>
                                <td><?= lang($payment->paid_by); ?></td>
                                <td>
                                    <div class="text-center">
                                        <a href="<?= site_url('purchases/payment_note/' . $id . '/' . $payment->id) ?>"
                                           data-toggle="modal" data-target="#myModal2"><i class="fa fa-file-text-o"></i></a>
										<a href="<?= site_url('purchases/edit_payment/' . $payment->id) ?>"
                                           data-toggle="modal" data-target="#myModal2"><i class="fa fa-edit"></i></a>
                                       
                                    </div>
                                </td>
                            </tr>
                        <?php
							$total += $payment->amount;
						}
                    } else {
                        echo "<tr><td colspan='4'>" . lang('no_data_available') . "</td></tr>";
                    } ?>
						<tr>
							<td></td>
							<td align="right">Current Balance:</td>
							<td><?=$this->erp->formatMoney($curr_balance);?><input type="hidden" name="total" id="total" value="<?=$this->erp->formatMoney($total);?>"></td>
							<td></td>
							<td></td>
						</tr>
						
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $(document).on('click', '.po-delete', function () {
            var id = $(this).attr('id');
            $(this).closest('tr').remove();
        });
    });
</script>    