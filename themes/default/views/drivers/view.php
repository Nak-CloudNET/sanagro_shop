<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header"> 
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
			<button type="button" class="btn btn-primary btn-xs no-print pull-right " onclick="window.print()">
				<i class="fa fa-print"></i>&nbsp;<?= lang("print"); ?>
			</button>
            <h4 class="modal-title" id="myModalLabel"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="margin-bottom:0;">
                    <tbody>
						<tr>
							<td><strong><?= lang("driver_name"); ?></strong></td>
							<td><?= $driver->name; ?></strong></td>
						</tr>
						<tr>
							<td><strong><?= lang("driver_code"); ?></strong></td>
							<td><?= $driver->code; ?></strong></td>
						</tr>
						<tr>
							<td><strong><?= lang("phone"); ?></strong></td>
							<td><?= $driver->phone; ?></strong></td>
						</tr>
						<tr>
							<td><strong><?= lang("email"); ?></strong></td>
							<td><?= $driver->email; ?></strong></td>
						</tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>
                <?php if ($Owner || $Admin || $GP['drivers-edit']) { ?>
                    <a href="<?=site_url('drivers/edit/'.$driver->id);?>" data-toggle="modal" data-target="#myModal2" class="btn btn-primary"><?= lang('edit'); ?></a>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
