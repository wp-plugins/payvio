<!-- Payvio Paywall Modal -->
<div id="modalPaywall" class="modal fade bs-example-modal-lg" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 500px;">
        <div class="modal-content">
            <div class="container" style="margin-top: 13px;margin-bottom:30px;margin-left:10px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-xs-12" style="padding-left: 400px;">
                                <a href="javascript:history.back()" class="btn btn-default btn-xs" style="margin-right: 15px; margin-top: 15px;">close</a>
                            </div>
                        </div>
                        Purchase:<span style="font-weight:bold;font-size:larger;margin-left:8px;"><?php echo($args[title]); ?></span>
                    </div>
                </div>  
                <br />
                <?php 
                  
                $redirectUri = urlencode(WORDPRESS_BASE_URL . "/?page_id=" . $args[contentId]);
                
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <a class="pv-link btn btn-primary btn-small" style="color:#ffffff"
                           data-pv-client-id="<?php echo($args[clientId]);?>"
                           data-pv-content-id="<?php echo($args[contentId]);?>"
                           data-pv-content-type="<?php echo($args[postType]);?>"     
                           data-pv-price="<?php echo($args[price]);?>"
                           data-pv-description="<?php echo($args[description]);?>"
                           data-pv-render-dot="None"
                           data-pv-redirect-uri="<?php echo($redirectUri); ?>"
                           data-pv-scope="user.charge"
                           data-pv-s="<?php echo($args[s]);?>"
                           data-pv-application-id="<?php echo($args[applicationId]);?>"
                           data-pv-content-ownership="alacarte"
                           data-pv-payviooauthauthurl="<?php echo($args[payvioOAuthAuthUrl]);?>"
                           href="#"
                        >Buy now for $ <?php echo($args[price] / 100.0);?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(true) { ?>
<script type="text/javascript">
    jQuery('#modalPaywall').modal('show');
</script>
<?php } ?>