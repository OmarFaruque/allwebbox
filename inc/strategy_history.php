<?php 

?>

<div class="history" id="strategy_history">
	<div class="history_inner">
		<div class="butto btn-group" style="text-align:right;">
			<a style="margin-right:20px;" href="<?php echo admin_url( $path = '/admin.php?page=my-menu&campains=1', $scheme = 'admin' ); ?>" class="button button-primary button-bigger"><?php echo __("Campaign's", 'allwebbox'); ?></a>
			<a href="<?php echo admin_url( $path = '/admin.php?page=my-menu', $scheme = 'admin' ); ?>" class="button button-primary button-bigger"><?php echo __('Objectives', 'allwebbox'); ?></a>
		</div>
		<h2><?php echo __('Strategy History', 'allwebbox'); ?></h2>
		<?php 
			$allStratgy = $wpdb->get_results('SELECT `scmp_name`, `type`, `cid`, `id`, `action_complete` FROM '.$tbl_subcampaign.'', OBJECT);
			/*echo '<pre>';
			print_r($allStratgy);
			echo '</pre>';*/
		?>
		<table id="historyList" class="table table-striped" style="width:100%;">
			<thead>
				<tr>
					<th><?php echo __('Campaign Name', 'allwebbox'); ?></th>
					<th><?php echo __('Sub Campaign Name', 'allwebbox'); ?></th>
					<th><?php echo __('Msg Type', 'allwebbox'); ?></th>
					<th style="max-width:300px;"><?php echo __('Completed Email', 'allwebbox'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($allStratgy as $sH):
					$campName = $wpdb->get_row('SELECT `cmp_name` FROM '.$tbl_campaign.' WHERE id='.$sH->cid.' ', OBJECT);
					$allEm = ($sH->action_complete != '')?json_decode($sH->action_complete):array();
				?>
					<tr>
						<td><?php echo $campName->cmp_name; ?></td>
						<td><?php echo $sH->scmp_name; ?></td>
						<td><?php echo $sH->type; ?></td>
						<td style="max-width:300px;"><?php echo implode(', ', $allEm); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>
</div>