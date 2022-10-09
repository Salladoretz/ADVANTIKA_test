<?
CModule::IncludeModule("iblock");

$ibId = 8; //Id инфоблока

$fileCSV = "test.csv"; //Путь к CSV-файлу
$arCSV = [];

if (($file = fopen($fileCSV, 'r')) !== false) {

    while (($data = fgetcsv($file, 1000, ';')) !== false) {

        $arCSV[] = $data;
    }

    fclose($file);
}


$arSelect = array("ID", "EXTERNAL_ID", "NAME", "IBLOCK_ID", "PREVIEW_TEXT", "DETAIL_TEXT");
$arFilter = array("IBLOCK_ID" => $ibId, "ACTIVE" => "Y");

$ibList = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);

while ($ob = $ibList->GetNextElement()) {

    $arFields = $ob->GetFields();

    $resProp1 = CIBlockElement::GetProperty($ibId, $arFields["ID"], "sort", "asc", array("CODE" => "prop1"));
    while ($ob = $resProp1->GetNext()) {
        $props1 = $ob['VALUE'];
    }

    $resProp2 = CIBlockElement::GetProperty($ibId, $arFields["ID"], "sort", "asc", array("CODE" => "prop2"));
    while ($ob = $resProp2->GetNext()) {
        $props2 = $ob['VALUE'];
    }

    $arFromIb[] = array(
        $arFields["EXTERNAL_ID"],
        $arFields["NAME"],
        $arFields["PREVIEW_TEXT"],
        $arFields["DETAIL_TEXT"],
        $props1,
        $props2
    );

    $arFromIbId[] = $arFields["ID"];
};


for ($i = 0; $i < count($arCSV); $i++) {
    if ($arFromIb[$i] != true) {

        $addElement = new CIBlockElement;

        $addProp = array();
        $addProp['prop1'] = $arCSV[$i][4];
        $addProp['prop2'] = $arCSV[$i][5];

        $arAddElement = array(
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => 8,
            "EXTERNAL_ID" => $arCSV[$i][0],
            "PROPERTY_VALUES" => $addProp,
            "NAME" => $arCSV[$i][1],
            "PREVIEW_TEXT" => $arCSV[$i][2],
            "DETAIL_TEXT" => $arCSV[$i][3],
            "ACTIVE" => "Y"
        );

        $addElement->Add($arAddElement);
    } else if ($arCSV[$i] !== $arFromIb[$i]) {

        $updateElement = new CIBlockElement;

        $updateProp = array();
        $updateProp['prop1'] = $arCSV[$i][4];
        $updateProp['prop2'] = $arCSV[$i][5];

        $arUpdateElement = array(
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => 8,
            "EXTERNAL_ID" => $arCSV[$i][0],
            "PROPERTY_VALUES" => $updateProp,
            "NAME" => $arCSV[$i][1],
            "PREVIEW_TEXT" => $arCSV[$i][2],
            "DETAIL_TEXT" => $arCSV[$i][3],
            "ACTIVE" => "Y"
        );

        $productId = $arFromIbId[$i];
        $updateElement->Update($productId, $arUpdateElement);
    }
}
