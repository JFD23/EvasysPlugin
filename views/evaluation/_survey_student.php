<? $min_user_permissions = EvasysPlugin::useLowerPermissionLevels() ? "user" : "autor" ?>
<? if (count($surveys)
    && $GLOBALS['perm']->have_studip_perm($min_user_permissions, Context::get()->id)
    && !$GLOBALS['perm']->have_studip_perm("dozent", Context::get()->id)) : ?>

    <? foreach ($evasys_seminar->getSurveys() as $survey_data) : ?>
        <? if ($survey_data->TransactionNumber && ($survey_data->TransactionNumber !== "null")) : ?>
            <? $_SESSION['EVASYS_SURVEY_TAN_EXISTED_'.Context::get()->id] = true ?>
            <?= MessageBox::info(_("Falls die Evaluation länger braucht zum Laden, drücken Sie bitte nicht auf Neuladen der ganzen Seite.")) ?>
            <iframe
                id="survey_<?= htmlReady($survey_data->TransactionNumber) ?>"
                style="width: 100%; height: 600px; border: 0px;"
                frameborder="0"
                allowfullscreen
                src="<?= htmlReady(Config::get()->EVASYS_URI."/indexstud.php?typ=html&user_tan=".urlencode($survey_data->TransactionNumber)) ?>">
            </iframe>
        <? else : ?>
            <? if ($_SESSION['EVASYS_SURVEY_TAN_EXISTED_'.Context::get()->id]) : ?>
                <?= MessageBox::success(_("Sie haben schon an der aktuellen Evaluation teilgenommen. Besten Dank!")) ?>
            <? else : ?>
                <?= MessageBox::info(_("Sie können nicht (mehr) an dieser Befragung teilnehmen.")) ?>
            <? endif ?>
            <? if ($evasys_seminar->publishingAllowed($dozent_id)) : ?>
                <?= $this->render_partial("evaluation/_survey_dozent.php", array(
                    'surveys' => $surveys,
                    'evasys_seminar' => $evasys_seminar
                )) ?>
            <? endif ?>
        <? endif ?>
    <? endforeach ?>
<? else : ?>
    <? if ($evasys_seminar->publishingAllowed($dozent_id)) : ?>
        <?= $this->render_partial("evaluation/_survey_dozent.php", array(
            'surveys' => $surveys,
            'evasys_seminar' => $evasys_seminar
        )) ?>
    <? else : ?>
        <?= MessageBox::info(_("Es gibt für Sie hier keine aktuellen, ausstehenden Evaluationen.")) ?>
    <? endif ?>
<? endif ?>
<script>
    jQuery("iframe[id^=survey_]").each(function (index, frame) {
        if (document.getElementById(frame.id).contentWindow.document) {
            jQuery(frame).css("height", document.getElementById(frame.id).contentWindow.document.body.scrollHeight);
        }
    });
</script>