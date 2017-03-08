<?php

namespace Drupal\entity_reference_autores\Plugin\Field\FieldFormatter;

use Drupal\entity_reference_categorized\Plugin\Field\FieldFormatter\EntityReferenceCategorizedFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * @FieldFormatter(
 *   id = "entity_reference_autores_formatter",
 *   label = @Translation("Entity label autores"),
 *   description = @Translation("Display referenced entities  as a list items gruped by category"),
 *   field_types = {
 *     "entity_reference_autores"
 *   }
 * )
 */
class EntityReferenceAutoresFormatter extends EntityReferenceCategorizedFormatter {

    public function viewElements(FieldItemListInterface $items, $langcode) {
        $elements = array();
        $output_as_link = $this->getSetting('link');
        $values = $items->getValue();

        foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {

            $label = ($values[$delta]['name_override']) ? $values[$delta]['name_override'] : $entity->label();

            // If the link is to be displayed and the entity has a uri, display a
            // link.
            if ($output_as_link && !$entity->isNew()) {
                try {
                    $uri = $entity->urlInfo();
                } catch (UndefinedLinkTemplateException $e) {
                    // This exception is thrown by \Drupal\Core\Entity\Entity::urlInfo()
                    // and it means that the entity type doesn't have a link template nor
                    // a valid "uri_callback", so don't bother trying to output a link for
                    // the rest of the referenced entities.
                    $output_as_link = FALSE;
                }
            }

            $category_type = $this->getFieldSetting('category_type');
            $category_entity = entity_load($category_type, $values[$delta]['category_id']);
            $category = ($category_entity->getWeight() > 0) ? ' (' . $category_entity->label() . ')' : '';

            //TODO: dar la opcion de lista LI o inline separado por comas.
            if ($output_as_link && isset($uri) && !$entity->isNew()) {
                $elements[$delta] = [
                    '#type' => 'link',
                    '#title' => $label,
                    '#url' => $uri,
                    '#suffix' => $category,
                    '#options' => $uri->getOptions(),
                ];

                if (!empty($items[$delta]->_attributes)) {
                    $elements[$delta]['#options'] += array('attributes' => array());
                    $elements[$delta]['#options']['attributes'] += $items[$delta]->_attributes;
                    // Unset field item attributes since they have been included in the
                    // formatter output and shouldn't be rendered in the field template.
                    unset($items[$delta]->_attributes);
                }
            } else {
                $elements[$delta] = array('#plain_text' => $label . $category);
            }
            $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();

            
            /* Descomentar estas lineas para usar el template definido en el modulo
            //variables para template. 
            $elements[$delta]['#theme'] = 'entity_reference_autores_formatter';
            $elements[$delta]['#url'] = $uri->toString();
            $elements[$delta]['#category'] = $category_entity->label();
            $elements[$delta]['#original_name'] = $entity->label();
            $elements[$delta]['#override_name'] = $values[$delta]['name_override'];
            unset($elements[$delta]['#suffix']);
            */
        }

        return $elements;
    }

}
