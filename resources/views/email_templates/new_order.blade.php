<!DOCTYPE html>
<html>
<head>
<title>New Purchase Order</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">
    /* CLIENT-SPECIFIC STYLES */
    body, table, td, a{-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
    table, td{mso-table-lspace: 0pt; mso-table-rspace: 0pt;} /* Remove spacing between tables in Outlook 2007 and up */
    img{-ms-interpolation-mode: bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */

    /* RESET STYLES */
    img{border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none;}
    table{border-collapse: collapse !important;}
    body{height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important;}

    /* iOS BLUE LINKS */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    /* MOBILE STYLES */
    @media screen and (max-width: 525px) {

        /* ALLOWS FOR FLUID TABLES */
        .wrapper {
          width: 100% !important;
            max-width: 100% !important;
        }

        /* ADJUSTS LAYOUT OF LOGO IMAGE */
        .logo img {
          margin: 0 auto !important;
        }

        /* USE THESE CLASSES TO HIDE CONTENT ON MOBILE */
        .mobile-hide {
          display: none !important;
        }

        .img-max {
          max-width: 100% !important;
          width: 100% !important;
          height: auto !important;
        }

        /* FULL-WIDTH TABLES */
        .responsive-table {
          width: 100% !important;
        }

        /* UTILITY CLASSES FOR ADJUSTING PADDING ON MOBILE */
        .padding {
          padding: 10px 5% 15px 5% !important;
        }

        .padding-meta {
          padding: 30px 5% 0px 5% !important;
          text-align: center;
        }

        .padding-copy {
             padding: 10px 5% 10px 5% !important;
          text-align: center;
        }

        .no-padding {
          padding: 0 !important;
        }

        .section-padding {
          padding: 50px 15px 50px 15px !important;
        }

        /* ADJUST BUTTONS ON MOBILE */
        .mobile-button-container {
            margin: 0 auto;
            width: 100% !important;
        }

        .mobile-button {
            padding: 15px !important;
            border: 0 !important;
            font-size: 16px !important;
            display: block !important;
        }

    }

    /* ANDROID CENTER FIX */
    div[style*="margin: 16px 0;"] { margin: 0 !important; }
</style>
</head>
<body style="margin: 0 !important; padding: 0 !important;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 15px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;" class="responsive-table">
                <tr>
                    <td>
                        <!-- COPY -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="font-size: 32px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Hello {{$recipient->firstname.' '.$recipient->lastname}}</td>
                            </tr>
                            <tr>
                                <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">A new purchase order has been raised by {{$sender->firstname.' '.$sender->lastname}} through <b>TynkerBox&trade;</b>. Below is a summary of the purchase order. Follow the link at the bottom of this email to approve or decline this purchase order.</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 15px;" class="padding">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;" class="responsive-table">
                <tr>
                    <td style="padding: 10px 0 0 0; border-top: 1px dashed #aaaaaa;">
                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top" class="mobile-wrapper">
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="left">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="left" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px;">Number of Items ({{$count}})</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- RIGHT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="right">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="right" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px;">${{$purchase_order->item_cost != null ? $purchase_order->item_cost : 0}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <!-- TWO COLUMNS -->
                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top" style="padding: 0;" class="mobile-wrapper">
                                    <!-- LEFT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="left">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="left" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px;">Sales Tax</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- RIGHT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="right">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="right" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px;">${{$purchase_order->sales_tax != null ? $purchase_order->sales_tax : 0}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <!-- TWO COLUMNS -->
                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top" style="padding: 0;" class="mobile-wrapper">
                                    <!-- LEFT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="left">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="left" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px;">Shipping Cost</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- RIGHT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="right">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="right" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px;">${{$purchase_order->shipping_cost != null ? $purchase_order->shipping_cost : 0}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <!-- TWO COLUMNS -->
                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top" style="padding: 0;" class="mobile-wrapper">
                                    <!-- LEFT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="left">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="left" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px;">Other Costs</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- RIGHT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="right">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="right" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px;">${{$purchase_order->other_cost != null ? $purchase_order->other_cost : 0}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0 0px 0; border-top: 1px solid #eaeaea; border-bottom: 1px dashed #aaaaaa;">
                        <!-- TWO COLUMNS -->
                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top" class="mobile-wrapper">
                                    <!-- LEFT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="left">
                                        <tr>
                                            <td style="padding: 0 0 10px 0;">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="left" style="font-family: Arial, sans-serif; color: #333333; font-size: 16px; font-weight: bold;">Total</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- RIGHT COLUMN -->
                                    <table cellpadding="0" cellspacing="0" border="0" width="47%" style="width: 47%;" align="right">
                                        <tr>
                                            <td>
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <td align="right" style="font-family: Arial, sans-serif; color: #7ca230; font-size: 16px; font-weight: bold;">${{$purchase_order->item_cost + $purchase_order->shipping_cost + $purchase_order->other_cost + $purchase_order->sales_tax != null ? $purchase_order->item_cost + $purchase_order->shipping_cost + $purchase_order->other_cost + $purchase_order->sales_tax : 0}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 15px;">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
            <tr>
            <td align="center" valign="top" width="500">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;" class="responsive-table">
                <tr>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <!-- COPY -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="left" style="padding: 0 0 0 0; font-size: 14px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color: #aaaaaa; font-style: italic;" class="padding-copy">{{$notes}}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 15px;">
            <table border="0" cellpadding="0" cellspacing="0" width="500" class="responsive-table">
                <tr>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center">
                                    <!-- BULLETPROOF BUTTON -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" style="padding-top: 25px;" class="padding">
                                                <table border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
                                                    <tr>
                                                        <td align="center" style="border-radius: 3px;" bgcolor="#256F9C">
                                                            <a href="{{URL::route('purchase-order.approval', $purchase_order->hash_link)}}" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; border-radius: 3px; padding: 15px 25px; border: 1px solid #256F9C; display: inline-block;" class="mobile-button">Manage Order</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 20px 0px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="max-width: 500px;" class="responsive-table">
                <tr>
                    <td align="left" style="font-size: 12px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;">
                        TynkerBox&trade;. Maintained and developed by <a href="http://codbitgh.com" target="_blank">Codbit Ghana Limited</a>
                        <br>
                        <span style="font-family: Arial, sans-serif; font-size: 12px; color: #444444;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                        <a href="#" target="_blank" style="color: #666666; text-decoration: none;">+233 203 598143</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
