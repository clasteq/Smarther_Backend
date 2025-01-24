<?php 
if(is_array($studentFeeStructures)) {  ?>

<div class="col-md-12 mt-3 mb-3" id=" "> 
    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
        <div class="table-responsicve">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Category</th>
                  <th>Item</th> 
                  <th>Balance Amount</th>
                  <th></th> 
                  <th>Concession Amount</th>
                  <th>Concession Remarks</th>
                </tr>
              </thead>  
              <tbody>
              	<?php foreach($studentFeeStructures as $fees) {
					$fees = $fees->toArray(); 
					if(count($fees)>0) {
						foreach($fees as $fee_items) {
							if(is_array($fee_items) && count($fee_items)>0) {
								foreach($fee_items as $items) {   ?>
									<tr>
										<td>{{$items['fee_item']['is_category_name']}}</td>
										<td>{{$items['fee_item']['item_name']}}</td>
										<td>{{$items['balance_amount']}}</td>
										<td><input type="checkbox" name="concessions[{{$items['id']}}]"></td>
										<td><input type="text" class="form-control concession_amount" name="concession_amount[{{$items['id']}}]"  minlength="1" maxlength="5" min="0" max="{{$items['balance_amount']}}"  onkeypress="return isNumber(event, this);" onkeyup=" checkbalance(this);"></td>
										<td><input type="text" class="form-control" name="concession_remarks[{{$items['id']}}]" minlength="3" maxlength="50"></td>
									</tr>
							<?php	}
							}
						}
					}
				?> 
				
			<?php  } ?>
              </tbody>
            </table>
        </div>
    </div> 
</div>
<?php  } ?>
</div>