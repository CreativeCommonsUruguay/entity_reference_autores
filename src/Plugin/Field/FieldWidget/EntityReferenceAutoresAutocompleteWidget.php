<?php

namespace Drupal\entity_reference_autores\Plugin\Field\FieldWidget;

use Drupal\entity_reference_categorized\Plugin\Field\FieldWidget\EntityReferenceCategorizedAutocompleteWidget;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "entity_reference_autores_autocomplete_widget",
 *   label = @Translation("Autocomplete w/Category Name Override"),
 *   description = @Translation("One autocomplete text field to select the entity and anothet to select the category."),
 *   field_types = {
 *     "entity_reference_autores"
 *   }
 * )
 */
class EntityReferenceAutoresAutocompleteWidget extends EntityReferenceCategorizedAutocompleteWidget {

    public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
        $element = parent::formElement($items, $delta, $element, $form, $form_state);


        $element['name_override'] = array(
            '#type' => 'textfield',
            '#title' => t('Nombre'),
            '#default_value' => $node->title,
            '#size' => 20,
            '#maxlength' => 200, //TODO: obtener valor de constante
            '#default_value' => (isset($items[$delta]->name_override) ) ? $items[$delta]->name_override : NULL,
            '#description' => t('Nombre para sobrescribir'),
            textfield
        );

        return $element;
    }

}
