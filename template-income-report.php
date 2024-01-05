<?php
/**
 * Template Name: Income / Expenses Report
 *
 * @author    eyorsogood.com, Rouie Ilustrisimo
 * @package   SwishDesign
 * @version   1.0.0
 */

/**
 * No direct access to this file.
 *
 * @since 1.0.0
 */

 $theme = new Theme();

get_header();
if ( have_posts() ) : ?>
	<?php while ( have_posts() ) { the_post(); ?>
		<div class="page-single">
			<main class="page-single__content" role="main">
                <div class="report__filter">
                    <form action="#" method="POST" id="filter-data" data-report="incomeexpense_report">
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-solid fa-calendar"></i></div><input type="text" placeholder="From" class="date-from"></div>
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-regular fa-calendar"></i></div><input type="text" placeholder="To" class="date-to"></div>
                        <div class="custom-post__filter" style="display: inline-block;">
                            <select class="select-filter income-cat">
                                <option value="all">All Income Categories</option>
                                <option value="Charges">Charges</option>
                                <option value="ECG">ECG</option>
                                <option value="Goods">Goods</option>
                                <option value="Laboratory Fees">Laboratory Fees</option>
                                <option value="OR Fees">OR Fees</option>
                                <option value="Others/Miscellaneous">Others/Miscellaneous</option>
                                <option value="Pharma Meds/Supplies">Pharma Meds/Supplies</option>
                                <option value="Professional Fees">Professional Fees</option>
                                <option value="PHIC (ACPN)">PHIC (ACPN)</option>
                                <option value="Physical Therapist">Physical Therapist</option>
                                <option value="Ultrasound">Ultrasound</option>
                                <option value="X-ray Fees">X-ray Fees</option>
                            </select>
                        </div>
                        <div class="custom-post__filter" style="display: inline-block;">
                            <select class="select-filter expense-cat">
                                <option value="all">All Expense Categories</option>
                                <option value="Advertisement">Advertisement</option>
                                <option value="Donations & Contribution">Donations & Contribution</option>
                                <option value="ECG Reading Fee">ECG Reading Fee</option>
                                <option value="Freight, Handling & Delivery">Freight, Handling & Delivery</option>
                                <option value="Fuel and Oil">Fuel and Oil</option>
                                <option value="Fringe Benefits">Fringe Benefits</option>
                                <option value="Goods Used">Goods Used</option>
                                <option value="Housekeeping Supplies">Housekeeping Supplies</option>
                                <option value="Laboratory Supplies Used">Laboratory Supplies Used</option>
                                <option value="Light, Water &Maintenance">Light, Water &Maintenance</option>
                                <option value="MAX Dr. Ched">MAX Dr. Ched</option>
                                <option value="Office Supplies Used">Office Supplies Used</option>
                                <option value="OR/WC Supplies Used">OR/WC Supplies Used</option>
                                <option value="Others">Others</option>
                                <option value="Personnel Benefits">Personnel Benefits</option>
                                <option value="Pharmacy Medicines/Supplies USed">Pharmacy Medicines/Supplies USed</option>
                                <option value="Professional Fees">Professional Fees</option>
                                <option value="PF PHIC">PF PHIC</option>
                                <option value="PF HMO">PF HMO</option>
                                <option value="PF DSWD">PF DSWD</option>
                                <option value="OTHER PF">OTHER PF</option>
                                <option value="Retainers Fee">Retainers Fee</option>
                                <option value="Representation & Entertainment">Representation & Entertainment</option>
                                <option value="Salaries">Salaries</option>
                                <option value="Seminars & Trainings">Seminars & Trainings</option>
                                <option value="Taxes, Licenses and Fees">Taxes, Licenses and Fees</option>
                                <option value="Transporation & Travel">Transporation & Travel</option>
                                <option value="Utilities">Utilities</option>
                                <option value="Wages">Wages</option>
                                <option value="Xray Supplies Used">Xray Supplies Used</option>
                            </select>
                        </div>
                        <div class="report__filter-btn"><a href="#" class="btn button"><i class="fa-solid fa-filter"></i> Apply Filter</a></div>
                    </form>
                </div>
                <div class="report__result" id="report__result">
                <!-- result goes here -->
                <?php echo $theme->getIncomeExpensesReport(date('01-m-Y'), date('d-m-Y', strtotime('last day of this month')), 'all', 'all');?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();