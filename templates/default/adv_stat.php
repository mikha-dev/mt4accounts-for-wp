<style>
.advanced {
    border-collapse: collapse;
    width: 100%;
    border-right: 1px solid #E2E1E1;
    border-left: 1px solid #E2E1E1;
}

.advanced td {
    border-top: 1px solid #F1F1F1;
    border-bottom: 1px solid #F1F1F1;
    border-collapse: collapse;
    padding: 0px 10px;
    line-height: 24px;
}

.adv_td {
  margin: 0px;
  padding: 0px;
}

.border0L {
    border-left: 0px none !important;
}

.border0R {
    border-right: 0px none !important;
}

.bg1 {
    background: #F8F8F8 none repeat scroll 0% 0%;
}

.alignR {
    text-align: right;
}

.blockHolder {
    width: 103px;
    border: 1px solid #F0EDED;
    text-align: left;
}

.greenBlock {
    height: 10px;
    background: #85E079 none repeat-x scroll 0% 0%;
}
.redBlock {
    height: 10px;
    background: #FE7F7F none repeat-x scroll 0% 0%;
}
.floatLeft {
    float: left;
}

.green {
    color: #00aa38;
}

.red {
    color: #FE7F7F;
}

.adv_stat {
  font-size: 12px;
  width: 100%;
}
</style>

<div style="display: flex; margin-left: auto; margin-left: auto; margin-right: auto;">
<div style="width:50%">
    <table class="advanced border0L">
        <tr class="bg1">
            <td>Trades:</td>
            <td class="alignR"><?php echo $account->nof_closed; ?></td>
        </tr>
        <tr>
            <td>Abs. Gain:</td>
            <td class="alignR <?php if($account->net_pl>=0) echo 'green'; else echo 'red'; ?>"><?php echo $account->net_pl; ?>%</td>
        </tr>
        <tr class="bg1">
            <td>Daily:</td>
            <td class="alignR"><?php echo $account->avg_daily_return; ?>%</td>
        </tr>
        <tr>
            <td>Monthly:</td>
            <td class="alignR"><?php echo $account->monthly_perc; ?>%</td>
        </tr>
        <tr>
            <td>Balance ($):</td>
            <td class="alignR"><?php if(!empty($GLOBALS)) echo $balance; else echo $account->balance; ?></td>
        </tr>
        <tr class="bg1">
            <td>Equity ($):</td>
            <td class="alignR"><?php if(!empty($equity)) echo $equity; else echo $account->equity; ?></td>
        </tr>
        <tr>
            <td>Profit ($):</td>
            <td class="alignR <?php if($account->net_profit>=0) echo 'green'; else echo 'red'; ?>"><?php echo $account->net_profit; ?></td>
        </tr>
        <tr class="bg1">
            <td>Interest ($):</td>
            <td class="alignR"><?php echo empty($account->interest) ? 0 : $account->interest; ?></td>
        </tr>
        <tr>
            <td>Deposits ($):</td>
            <td class="alignR"><?php echo $account->deposit; ?></td>
        </tr>
        <tr class="bg1">
            <td>Withdrawals ($):</td>
            <td class="alignR"><?php echo empty($account->withdrawal) ? 0 : $account->withdrawal; ?></td>
        </tr>
        <tr>
            <td>Commissions ($):</td>
            <td class="alignR"><?php echo $account->total_commission; ?></td>
        </tr>
    </table>
</div>
<div style="width:50%">
    <table class="advanced border0R border0L">
        <tr class="bg1">
            <td>Profitability:</td>
            <td class="pointer" style="float: right;">
            <div class="blockHolder">
                <div class="greenBlock floatLeft" style="width:<?php echo number_format($account->nof_winning/$account->nof_closed*100, 0); ?>px"></div>
                <div class="redBlock floatLeft" style="width:<?php echo number_format($account->nof_lossing/$account->nof_closed*100, 0); ?>px"></div>
            </div>
            </td>
        </tr>
        <tr>
            <td>Pips:</td>
            <td class="alignR"><?php echo $account->net_profit_pips; ?></td>
        </tr>
        <tr class="bg1">
            <td nowrap>Average Win (pips/$):</td>
            <td class="alignR"><?php echo $account->avg_win_pips.' / '.$account->avg_win; ?></td>
        </tr>
        <tr>
            <td nowrap>Average Loss (pips/$):</td>
            <td class="alignR"><?php echo $account->avg_loss_pips.' / '.$account->avg_loss; ?></td>
        </tr>
        <tr class="bg1">
            <td>Lots:</td>
            <td class="alignR"><?php echo $account->total_lots; ?></td>
        </tr>
        <tr class="bg1">
            <td>Longs Won:</td>
            <td class="alignR"><span class="gray">(<?php if($account->total_longs == 0) echo '0'; else echo $account->longs_won.'/'.$account->total_longs; ?>)</span> <?php if($account->total_longs==0) echo '0'; else echo number_format( $account->longs_won/$account->total_longs*100, 2 ); ?>%</td>
        </tr>
        <tr>
            <td>Shorts Won:</td>
            <td class="alignR"><span class="gray">(<?php if($account->total_shorts ==0) echo '0'; else echo $account->shorts_won.'/'.$account->total_shorts; ?>)</span> <?php echo number_format( $account->shorts_won/$account->total_shorts*100, 2 ); ?>%</td>
        </tr>
        <tr class="bg1">
            <td>Best Trade($):</td>
            <td class="alignR"><?php echo $account->best_trade_dol; ?></td>
        </tr>
        <tr>
            <td> Worst Trade($):</td>
            <td class="alignR"><?php echo $account->worst_trade_dol; ?></td>
        </tr>
        <tr class="bg1">
            <td>Best Trade (Pips):</td>
            <td class="alignR"><?php echo $account->best_trade_pips; ?></td>
        </tr>
        <tr>
            <td>Worst Trade (Pips):</td>
            <td class="alignR"><?php echo $account->worst_trade_pips; ?></td>
        </tr>
        <tr class="bg1">
            <td></td>
            <td class="alignR"></td>
        </tr>
    </table>
</div>
</div>