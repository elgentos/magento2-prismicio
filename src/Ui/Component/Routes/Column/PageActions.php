<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Ui\Component\Routes\Column;

class PageActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Prepare the data source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                $id   = "X";

                if (isset($item["route_id"])) {
                    $id = $item["route_id"];
                }

                $item[$name]["view"] = [
                    "href" => $this->getContext()->getUrl(
                        "prismicio/routes/edit",
                        ["route_id" => $id]
                    ),
                    'label' => __('Edit')
                ];
            }
        }

        return $dataSource;
    }
}
