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
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 *
 * @FieldType(
 *   id = "entity_reference_autores",
 *   label = @Translation("Entity reference autores"),
 *   description = @Translation("An entity field containing an entity reference with a category and a Name Voerride."),
 *   category = @Translation("Reference"),
 *   default_widget = "entity_reference_autores_autocomplete_widget",
 *   default_formatter = "entity_reference_autores_formatter",
 *   list_class = "\Drupal\entity_reference_categorized\Plugin\Field\FieldType\EntityReferenceCategorizedFieldItemList",
 *
 * )
 */
class EntityReferenceAutores extends EntityReferenceCategorized {

    /**
     * max lenth for the name override
     * is used in schema so if chenged may affect olready instantiated fields
     */
    const NAME_OVERRIDE_MAXLENGTH = 200;

    /**
     * {@inheritdoc}
     */
    public static function defaultFieldSettings() {
        return array(
            'name_override' => array(),
                ) + parent::defaultFieldSettings();
        ;
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
                'description' => 'Nombre para sobreescribir el Nombre/título del autor referenciado.',
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
                    'maxMessage' => t('%name: El largo máximo para el nombre es @max caracteres.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => static::NAME_OVERRIDE_MAXLENGTH)), //TODO:posible error en $this->getFIleDefinition
                )
            ),
        ));
        return $constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
        $form = parent::fieldSettingsForm($form, $form_state);
        //TODO: hay configuraciones particulares?
        return $form;
    }

    /**
     * Do not want preconfigured options (at least for the moment)
     * @return type
     */
    public static function getPreconfiguredOptions() {
        return null; 
    }

}
