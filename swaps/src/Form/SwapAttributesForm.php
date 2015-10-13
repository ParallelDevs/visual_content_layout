<?php
/**
 * @file
 * Contains \Drupal\visual_content_layout\Form\VisualContentLayoutForm.
 */

namespace Drupal\swaps\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\SettingsCommand;
use Drupal\Core\Ajax\AjaxResponse;

/**
 * Contribute form.
 */
class SwapAttributesForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'swap_attributes_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $name = NULL) {

    //get all the swaps plugins
    $manager = \Drupal::service('plugin.manager.swaps');
    $swaps = $manager->getDefinitions();

    //search the swap that have the name of the variable $name
    foreach($swaps as $swap){
      if($swap['name'] == $name){
        break;
      }
    }

    //store the name of the swap
    $form['swap'] = array('#type' => 'hidden', '#value' => $name);

    //create the tab for the swap attributes items
    $form['formTabs'] = array(
      '#type' => 'vertical_tabs',
      '#default_tab' => 'swapAttributes',
    );

    $form['swapAttributes'] = array(
      '#type' => 'details',
      '#title' => 'Swap',
      '#group' => 'formTabs',
    );

    $form['class'] = array(
      '#type' => 'details',
      '#title' => 'Class',
      '#group' => 'formTabs',
    );

    //get the attributes annotation and split by the ","
    $attributes = $swap['attributes'];
    $attributesList = explode(',', $attributes);

    //process all the attributes of the swap
    foreach($attributesList as $attr){

      //get the name and type of the current attribute
      $attr = substr(trim($attr), 1, -1);
      $attr = explode('|', $attr);
      $name = trim($attr[1]);
      $type = trim($attr[2]);

      //process depending on the name
      switch ($type) {

        //---------------------------------------------------------------
        //                   processing text attributes
        //---------------------------------------------------------------
        case 'text':

          $id = $name.'_textfield';

          $form['swapAttributes'][$name] = array(
            '#type' => 'textfield',
            '#title' => trim($attr[0]),
            '#size' => 60,
            '#attributes' => array('id' => array($id)),
          );
          break;

        //---------------------------------------------------------------
        //                   processing boolean attributes
        //---------------------------------------------------------------
        case 'boolean':
          break;

        //---------------------------------------------------------------
        //                   processing color attributes
        //---------------------------------------------------------------
        case 'color':

          $id =  $name . '_colorpicker';

          $form['swapAttributes'][$name] = array(
            '#type' => 'textfield',
            '#title' => trim($attr[0]),
            '#size' => 60,
            '#default_value' => '#123456',
            '#attributes' => array('id' => array($id),
                                   'class' => array('colorpicker_input')),
          );
          break;

        //---------------------------------------------------------------
        //                   processing select attributes
        //---------------------------------------------------------------
        case 'select':

          $id = $name . '_select';

          //get the options
          $options = trim($attr[3]);
          //validate the separate symbol for int select o string select
          if(strpos($options, "-") === FALSE){
            $elements = explode("," , $options);
            $options = array();
            foreach($elements as $element){
              $options[$element] = $element;
            }
          }else{
            //get the first and the last number of the sequence
            list($first, $last) = explode('-', $options);
            //validate are numbers
            if(!is_numeric($first) || !is_numeric($last)){
              break;
            }
            //validate the first is greater than the last
            if($first>$last){
              $aux = $first;
              $first = $last;
              $last = $aux;
            }
            $options = array();
            for($i = $first; $i<= $last; $i++){
              $options[$i] = $i;
            }
          }
          //create the form with the options
          $form['swapAttributes'][$name] = array(
            '#type' => 'select',
            '#title' => trim($attr[0]),
            '#options' => $options,
            '#attributes' => array('id' => array($id)),
          );
          break;

        //---------------------------------------------------------------
        //                    obviate others attributes
        //---------------------------------------------------------------
        default:
          break;
      }

    }

    //create the form with the options
    $form['class']['swapClass'] = array(
      '#type' => 'textfield',
      '#title' => 'Class',
      '#size' => 60,
    );


    $form['accept'] = array(
      '#type' => 'submit',
      '#value' => t('Accept'),
      '#group' => 'swapAttributes',
      '#ajax' => array(
        'callback' => '::ajaxSubmit',
      ),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {


  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {


  }

  public function ajaxSubmit(array &$form, FormStateInterface $form_state){

    //---------------------------------------------------------------
    //            get the own attributes values of the swap
    //---------------------------------------------------------------

    //get all the swaps plugins
    $manager = \Drupal::service('plugin.manager.swaps');
    $swaps = $manager->getDefinitions();

    $input = $form_state->getUserInput();
    $settings = array();

    //search the swap that have the name of the variable $name
    foreach($swaps as $swap){
      if($swap['name'] == $input['swap']){
        break;
      }
    }

    //get the attributes annotation and split by the ","
    $attributes = $swap['attributes'];
    $attributesList = explode(',', $attributes);

    //process all the attributes of the swap
    foreach($attributesList as $attr) {

      //get the name of the current attribute
      $name = explode(':', $attr);
      $name = explode('[', $name[0]);
      $name = trim($name[0]);

      $settings[$name] = $input[$name];

    }

    //get all de default attributes input
    $settings['class'] = $input['swapClass'];


    //---------------------------------------------------------------
    //            get the default attributes values of the swap
    //---------------------------------------------------------------

    $settings['swapId'] = $swap['id'];
    $settings['swapName'] = $swap['name'];
    $settings['container'] = $swap['container'];

    //---------------------------------------------------------------
    //            create the ajax response
    //---------------------------------------------------------------

    $visualSettings = array('visualContentLayout' => array(
                                'attributes' => $settings));
    $response = new AjaxResponse();
    $response->addCommand(new CloseModalDialogCommand());
    $response->addCommand(new SettingsCommand($visualSettings,FALSE));

    return $response;


  }
}