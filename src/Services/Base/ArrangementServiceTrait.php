<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.04.18
 * Time: 21:34
 */

namespace Jinya\Services\Base;

trait ArrangementServiceTrait
{
    /**
     * @param array $positions
     * @param int $oldPosition
     * @param int $newPosition
     * @param $targetItem
     * @return mixed
     */
    private function rearrange(array $positions, int $oldPosition, int $newPosition, $targetItem)
    {
        uasort($positions, function ($a, $b) {
            /* @noinspection PhpUndefinedMethodInspection */
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        if ($oldPosition < $newPosition) {
            array_splice($positions, $newPosition + 1, 0, [$targetItem]);

            if ($oldPosition > -1) {
                array_splice($positions, $oldPosition, 1);
            }
        } else {
            array_splice($positions, $newPosition, 0, [$targetItem]);

            if ($oldPosition > -1) {
                array_splice($positions, $oldPosition + 1, 1);
            }
        }

        array_walk($positions, function (&$item, int $index) {
            /* @noinspection PhpUndefinedMethodInspection */
            $item->setPosition($index);
        });

        return $positions;
    }
}
