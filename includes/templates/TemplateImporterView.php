<?php

use MerryCode\ColorBasedProductImport\Configs\Main;

/** @var array $data */
$status = $data["status"];
?>
<div class="ishark-import-wrapper">
	<div class="import-header">
		<ul>
			<li class="step <?= ($status == Main::IMPORT_STATUS_IDLE) ? 'active' : '' ?>">
                <?= __("Upload CSV file", Main::TEXT_DOMAIN) ?>
            </li>
			<li class="step <?= ($status == Main::IMPORT_STATUS_IN_PROGRESS) ? 'active' : '' ?>">
				<?= __("Importing", Main::TEXT_DOMAIN) ?>
            </li>
			<li class="step <?= ($status == Main::IMPORT_STATUS_DONE) ? 'active' : '' ?>">
				<?= __("Done", Main::TEXT_DOMAIN) ?>
            </li>
		</ul>
	</div>
	<div class="panel">
		<div class="upload-file <?= ($status == 'idle') ? '' : 'hidden' ?>">
			<div class="card-title">
				<h3><?= __("Import Products from a CSV File", Main::TEXT_DOMAIN); ?></h3>
			</div>
			<div class="card-details">
				<p><?= __("This tool allows you to import iShark standard csv files", Main::TEXT_DOMAIN); ?></p>
			</div>
			<hr/>
			<div class="import-form">
				<div class="row">
					<div class="column">
						<p><?= __("Choose a CSV file from your computer:", Main::TEXT_DOMAIN); ?></p>
					</div>
					<div class="column">
						<p><input type="file" id="ishark_file_upload" name="ishark-csv-uploader"/></p>
					</div>
				</div>
				<div class="row">
					<div class="column">
						<p><?= __("Specify CSV Delimiter:", Main::TEXT_DOMAIN); ?></p>
					</div>
					<div class="column">
						<p>
                            <input
                                    type="text"
                                    id="ishark_delimiter"
                                    name="ishark-csv-delimiter"
                                    value=";"
                                    maxlength="1"
                            />
                        </p>
					</div>
				</div>
				<hr/>
				<button
                        type="submit"
                        id="ishark_file_submit"
                        class="button button-primary button-next button-ishark"
                        value="Continue"
                        name="save_step"
                >
					<?= __("Continue", Main::TEXT_DOMAIN) ?>
                </button>
			</div>
		</div>

		<div class="import-in-progress <?= ($status == 'in-progress') ? '' : 'hidden' ?>">
			<h1><?= __("Import in progress", Main::TEXT_DOMAIN); ?></h1>
			<p><div class="loader"></div></p>
			<p><?= __("The import is running in the background. You may navigate away or nabigate away or refresh this page to check the progress.", Main::TEXT_DOMAIN); ?></p>
			<form method="post" action="">
				<button
                        type="submit"
                        id="ishark_refresh"
                        class="button button-primary button-next button-ishark"
                        value="Continue"
                        name="save_step"
                >
					<?= __("Refresh Page", Main::TEXT_DOMAIN) ?>
                </button>
			</form>

		</div>
		<div class="import-done <?= ($status == Main::IMPORT_STATUS_DONE) ? '' : 'hidden' ?>">
			<h1><?= __("Import was completed", Main::TEXT_DOMAIN); ?></h1>
			<p><img src="data:image/png;base64,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"/></p>
			<p><?= __("Please go to products section to check if your import was successful.", Main::TEXT_DOMAIN); ?></p>

			<form method="post" action="">
				<input type="hidden" name="<?= Main::POST_ARGUMENT_COMMAND ?>" value="new_import">
				<button
                        type="submit"
                        id="ishark_new_import"
                        class="button button-primary button-next button-ishark"
                        value="Continue"
                        name="save_step"
                >
					<?= __("New Import", Main::TEXT_DOMAIN) ?>
                </button>
			</form>
		</div>
	</div>
</div>