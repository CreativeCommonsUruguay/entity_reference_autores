<?php

namespace Drupal\entity_reference_autores\Plugin\Field\FieldType;

use Drupal\entity_reference_categorized\Plugin\Field\FieldType\EntityReferenceCategorized;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataReferenceTargetDefinition;
use Drupal\Core\TypedData\DataReferenceDefinition;
use Drupal\Core\Entity\TypedData\EntityDataDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 * @FieldType(
 *   id = "entity_reference_autores",
 *   label = @Translation("Entity reference Autores"),
 *   description = @Translation("An entity field containing an entity reference with a category and a Name Voerride."),
 *   category = @Translation("Reference"),
 *   default_widget = "entity_reference_autores_autocomplete_widget",
 *   default_formatter = "entity_reference_autores_formatter",
 *   list_class = "\Drupal\entity_reference_categorized\Plugin\Field\FieldType\EntityReferenceCategorizedFieldItemList",
 *
 * )
 */
class EntityReferenceAutores extends EntityReferenceCategorized {

    const NAME_OVERRIDE_MAXLENGTH = 200;

    /**
     * {@inheritdoc}
     */
    public static function defaultFieldSettings() {
        return array(
                //TODO: agregar valores por defecto para la configuracion del campo
                //'category_bundle' => array(),
                ) + parent::defaultFieldSettings();
    }

    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
        $properties = parent::propertyDefinitions($field_definition);

        $properties['name_override'] = DataDefinition::create('string')
                ->setLabel(t('Sobreescribir nombre'));

        return $properties;
    }

    public static function schema(FieldStorageDefinitionInterface $field_definition) {
        $schema = parent::schema($field_definition);

        $columns = array(
            'name_override' => array(
                'description' => 'Nombre para sobreescribir el Nombre/titulo del autor referenciado.',
                'type' => 'varchar_ascii',
                'length' => static::NAME_OVERRIDE_MAXLENGTH,
                'not null' => FALSE,
            ),
        );
        $indexes = array(
            'name_override' => array('name_override')
        );

        //anexamos nuestro esquema para la configuracion al de EntiryReference
        $schema['columns'] += $columns;
        $schema['indexes'] += $indexes;

        return $schema;
    }
    
    /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'name_override' => array(
        'Length' => array(
          'max' => static::NAME_OVERRIDE_MAXLENGTH,
          'maxMessage' => t('%name: El largo máximo para el nombre es @max caracteres.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => static::NAME_OVERRIDE_MAXLENGTH)),   //TODO:posible error en $this->getFIleDefinition
        )
      ),
    ));
    return $constraints;
  }

    /**
     * {@inheritdoc}
     */
//    public function isEmpty() {
//        $value = $this->get('name_override')->getValue();
//        return $value === NULL || trim($value) === '' ;
//    }

    /**
     * {@inheritdoc}
     */
    public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
        $form = parent::fieldSettingsForm($form, $form_state);

//        $field = $form_state->getFormObject()->getEntity();
//        $category_type = $this->getSetting('category_type');   
//        $form['autores_settings'] = array(
//            '#type' => 'details',
//            '#title' => t('Autores Settings'),
//            '#open' => TRUE,
//            '#tree' => TRUE,
//            '#process' => array(array(get_class($this), 'formProcessMergeParent')),
//        );
//
//        $form['autores_settings']['setting1'] = array(
//            '#type' => 'select',
//            '#title' => t('Category taxonomy'),
//            '#options' => $options,
//            '#default_value' => $field->getSetting('category_bundle'),
//            '#required' => TRUE,
//            '#ajax' => TRUE,
//            '#limit_validation_errors' => array(),
//        );
        //TODO: analizar dependenias al igual que EntityReference
        //TODO: permitir crear entidades si no existen para usar una taxonomia libre.
        //      Ver configuracion de auto_create de ER

        return $form;
    }

}
