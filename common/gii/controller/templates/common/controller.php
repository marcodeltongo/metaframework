<?php
/**
 * This is the template for generating a controller class file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 */

echo "<?php\n"; ?>

class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{

    public function accessRules()
    {
        return array(
            /*
            array('deny',
                'actions' => array('delete'),
                'users' => array('*'),
            ),
            //*/
        );
    }

<?php foreach($this->getActionIDs() as $action): ?>
    /**
     * <?php echo ucfirst($action), PHP_EOL; ?>
     */
    public function action<?php echo ucfirst($action); ?>()
    {
        $this->render('<?php echo $action; ?>');
    }

<?php endforeach; ?>
}