// https://www.w3resource.com/javascript/form/email-validation.php
function validate_email($test_me) 
{
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($test_me))
  {
    return (true)
  }
  return (false)
} //  validate_email()
