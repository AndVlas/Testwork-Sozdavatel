<?php

//========================================
// Блок FAQ Вопрос-ответ
//========================================

$eventType = CEventType::GetList(array("TYPE_ID" => "FAQ_ANSWER"));
if (!$eventType->Fetch()) {
    // Создаём тип события
    $et = new CEventType;
    $et->Add(array(
        "LID" => "ru", // Языковой ID
        "EVENT_NAME" => "FAQ_ANSWER",
        "NAME" => "Отправка ответа на вопрос FAQ",
        "DESCRIPTION" => "#QUESTION_TEXT# - Текст вопроса\n#ANSWER_TEXT# - Текст ответа\n#EMAIL_TO# - Email получателя\n#USER_NAME# - Имя пользователя (если есть)\n#SITE_NAME# - Название сайта\n#SERVER_NAME# - URL сайта"
    ));
}

// Обработчик отправки письма
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "SendAnswerOnFaqUpdate");

function SendAnswerOnFaqUpdate(&$arFields)
{
    $faqIBlockId = 2; // ID инфоблока

    if ($arFields["IBLOCK_ID"] != $faqIBlockId || !$arFields["RESULT"]) {
        return;
    }

    static $isHandlerRunning = false;
    if ($isHandlerRunning) {
        return;
    }
    $isHandlerRunning = true;

    $dbOldElement = CIBlockElement::GetList(
        array(),
        array("ID" => $arFields["ID"], "IBLOCK_ID" => $faqIBlockId),
        false,
        false,
        array("ID", "DETAIL_TEXT", "PREVIEW_TEXT", "NAME")
    );

    if ($arOldElement = $dbOldElement->Fetch()) {
        $oldDetailText = trim($arOldElement["DETAIL_TEXT"]);
        $newDetailText = trim($arFields["DETAIL_TEXT"]);

        if (empty($oldDetailText) && !empty($newDetailText)) {

            $userEmail = '';
            $dbProps = CIBlockElement::GetProperty(
                $faqIBlockId,
                $arFields["ID"],
                array("sort" => "asc"),
                array("CODE" => "EMAIL")
            );

            if ($arProp = $dbProps->Fetch()) {
                $userEmail = $arProp["VALUE"];
            }

            $questionText = !empty($arFields["PREVIEW_TEXT"])
                ? $arFields["PREVIEW_TEXT"]
                : $arOldElement["PREVIEW_TEXT"];

            $questionName = !empty($arFields["NAME"])
                ? $arFields["NAME"]
                : $arOldElement["NAME"];

            if (!empty($userEmail) && check_email($userEmail)) {

                $arEventFields = array(
                    "QUESTION_TEXT" => $questionText,
                    "ANSWER_TEXT"   => $newDetailText,
                    "EMAIL_TO"      => $userEmail,
                    "USER_EMAIL"    => $userEmail,
                    "USER_NAME"     => $questionName,
                    "SITE_NAME"     => COption::GetOptionString("main", "site_name", ""),
                    "SERVER_NAME"   => SITE_SERVER_NAME,
                    "QUESTION_ID"   => $arFields["ID"],
                );

                $result = CEvent::Send("FAQ_ANSWER", SITE_ID, $arEventFields);

                if ($result) {
                    AddMessage2Log(
                        "FAQ: Ответ отправлен. ID вопроса: " . $arFields["ID"] .
                            ", Email: " . $user_email,
                        "faq_module"
                    );
                }
            }
        }
    }

    $isHandlerRunning = false;
}
