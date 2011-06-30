<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>

/**
 * CRUD controller to manage the <?php echo $this->modelClass; ?> model.
 */
class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseControllerClass."\n"; ?>
{
    /**
     * Managed model class
     *
     * @var string
     */
    protected $modelClass = '<?php echo $this->modelClass; ?>';
}
