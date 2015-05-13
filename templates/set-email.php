<?php
/*
* Set Email
*/
if( is_user_logged_in() && !current_user_can( 'manage_options' ) ):
?>
<script>
var _ra = _ra || {};

_ra.setEmailInfo = {
    "email": "<?php echo $email['email']; ?>"
};

if (_ra.ready !== undefined) {
_ra.setEmail(_ra.setEmailInfo)
}
</script>

<?php endif; ?>