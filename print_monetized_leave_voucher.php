<!-- PRINT DISBURSEMENT VOUCHER FOR MONETIZED LEAVE -->
<div id="print_monetized_voucher_<?php echo $la_row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="printMonetizedVoucherLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-xl" style="max-width: 1200px;">
    <div class="modal-content">
    
      <div class="modal-header bg-primary text-white">
        <h5 id="printMonetizedVoucherLabel" class="modal-title">
          <i class="fa fa-print"></i> DISBURSEMENT VOUCHER
        </h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body p-4" id="print_monetized_voucher_content_<?php echo $la_row['id']; ?>" style="max-height: 80vh; overflow-y: auto;">
        <div class="voucher-container">
          
          <!-- Header -->
          <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
            <div style="flex: 1;">
              <?php if (!empty($sf_row['logo']) && file_exists('img/' . $sf_row['logo'])): ?>
                <img src="img/<?php echo htmlspecialchars($sf_row['logo']); ?>" alt="Logo" style="width: 70px; height: 70px; margin-bottom: 10px; display: block;">
              <?php else: ?>
                <div class="seal-placeholder" style="width: 70px; height: 70px; border: 2px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                  <small>SEAL</small>
                </div>
              <?php endif; ?>
              <div style="font-size: 10px;">Republic of the Philippines</div>
              <div style="font-size: 10px;"><?php echo htmlspecialchars($sf_row['region'] ?? 'Province of Negros Occidental'); ?></div>
              <div style="font-size: 11px; font-weight: bold;"><?php echo htmlspecialchars($sf_row['institution_name'] ?? 'Municipality of Hinoba-an'); ?></div>
              <div style="font-size: 10px;">OFFICE OF THE MUNICIPAL MAYOR</div>
            </div>
            <div style="flex: 1; text-align: center;">
              <div style="font-weight: bold; font-size: 14px; margin-top: 20px;">DISBURSEMENT VOUCHER</div>
              <div style="font-size: 11px; margin-top: 5px;"><?php echo htmlspecialchars($sf_row['institution_name'] ?? 'Municipality of Hinoba-an'); ?></div>
              <div style="font-size: 10px; font-style: italic;">LGU</div>
            </div>
            <div style="flex: 1; text-align: right;">
              <div style="border: 1px solid #000; padding: 5px; display: inline-block; margin-top: 20px;">
                <div><strong>Fund:</strong></div>
                <div><strong>DV No.:</strong> <span id="voucher_dv_no_<?php echo $la_row['id']; ?>">______________</span></div>
                <div><strong>Date:</strong> <span id="voucher_date_<?php echo $la_row['id']; ?>">______________</span></div>
              </div>
            </div>
          </div>
          
          <!-- Payee Information -->
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
              <td style="border: 1px solid #000; padding: 5px; width: 70%;" colspan="2">
                <div><strong>Payee:</strong></div>
                <div style="margin: 5px 0; font-size: 12px;" id="voucher_payee_<?php echo $la_row['id']; ?>"></div>
              </td>
              <td style="border: 1px solid #000; padding: 5px;" rowspan="2">
                <div><strong>ID No./TIN:</strong></div>
                <div><strong>GAPA No.:</strong></div>
                <div><strong>Responsibility Center:</strong></div>
              </td>
            </tr>
            <tr>
              <td style="border: 1px solid #000; padding: 5px;" colspan="2">
                <div><strong>Address:</strong> <span id="voucher_address_<?php echo $la_row['id']; ?>"></span></div>
              </td>
            </tr>
          </table>
          
          <!-- Particulars -->
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
              <td style="border: 1px solid #000; padding: 10px; vertical-align: top;">
                <div><strong>Particulars</strong></div>
                <div style="margin-top: 10px; min-height: 60px;" id="voucher_particulars_<?php echo $la_row['id']; ?>"></div>
              </td>
              <td style="border: 1px solid #000; padding: 10px; width: 150px; text-align: right; vertical-align: top;">
                <div><strong>Amount</strong></div>
                <div style="margin-top: 10px; font-size: 14px; font-weight: bold;" id="voucher_amount_<?php echo $la_row['id']; ?>"></div>
              </td>
            </tr>
            <tr>
              <td style="border: 1px solid #000; padding: 5px;" colspan="2">
                <div><strong>Amount Due:</strong> <span style="font-weight: bold;" id="voucher_amount_due_<?php echo $la_row['id']; ?>"></span></div>
              </td>
            </tr>
          </table>
          
          <!-- Certifications Section -->
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
              <td style="border: 1px solid #000; padding: 10px; width: 33%; vertical-align: top;">
                <div style="font-weight: bold;">A. Certified:</div>
                <div style="font-size: 10px; margin: 5px 0;">Expenses/Cash Advances necessary, valid, proper, lawful and incurred under my direct supervision.</div>
                <div style="margin-top: 25px; text-align: center;">
                  <div style="font-weight: bold; text-decoration: overline;" id="voucher_admin_<?php echo $la_row['id']; ?>">____________________________</div>
                  <div style="font-size: 10px;" id="voucher_admin_position_<?php echo $la_row['id']; ?>">Municipal Administrator</div>
                  <div style="font-size: 10px;">Signature Over Printed Name/Position</div>
                  <div style="font-size: 10px;">Head of the Agency/authorized representative</div>
                </div>
              </td>
              
              <td style="border: 1px solid #000; padding: 10px; width: 33%; vertical-align: top;">
                <div style="font-weight: bold;">B. Certified:</div>
                <div style="font-size: 10px; margin: 5px 0;">Completeness and propriety of supporting documents/previous cash advance liquidated/evidence of prior held in trust.</div>
                <div style="margin-top: 25px; text-align: center;">
                  <div style="font-weight: bold; text-decoration: overline;" id="voucher_accountant_<?php echo $la_row['id']; ?>">____________________________</div>
                  <div style="font-size: 10px;" id="voucher_accountant_position_<?php echo $la_row['id']; ?>">Municipal Accountant</div>
                  <div style="font-size: 10px;">Head of Accounting Department/Office</div>
                </div>
              </td>
              
              <td style="border: 1px solid #000; padding: 10px; width: 34%; vertical-align: top;">
                <div style="font-weight: bold;">C. Certified:</div>
                <div style="font-size: 10px; margin: 5px 0;">Funds available for the purpose.</div>
                <div style="margin-top: 25px; text-align: center;">
                  <div style="font-weight: bold; text-decoration: overline;" id="voucher_treasurer_<?php echo $la_row['id']; ?>">____________________________</div>
                  <div style="font-size: 10px;" id="voucher_treasurer_position_<?php echo $la_row['id']; ?>">Acting Municipal Treasurer</div>
                  <div style="font-size: 10px;">Signature Over Printed Name/Position</div>
                  <div style="font-size: 10px;">Head of Treasury Department Office</div>
                </div>
              </td>
            </tr>
          </table>
          
          <!-- Approval and Payment Section -->
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
              <td style="border: 1px solid #000; padding: 10px; width: 50%; vertical-align: top;">
                <div style="font-weight: bold;">D. Approved For Payment: <span id="voucher_approved_amount_<?php echo $la_row['id']; ?>">₱______________</span></div>
                <div style="margin-top: 20px; text-align: center;">
                  <div style="font-weight: bold; text-decoration: overline;" id="voucher_mayor_<?php echo $la_row['id']; ?>">____________________________</div>
                  <div style="font-size: 10px;" id="voucher_mayor_position_<?php echo $la_row['id']; ?>">Municipal Mayor</div>
                  <div style="font-size: 10px;">Signature Over Printed Name/Position</div>
                  <div style="font-size: 10px;">Local Chief Executive</div>
                </div>
              </td>
              
              <td style="border: 1px solid #000; padding: 10px; width: 50%; vertical-align: top;">
                <div style="font-weight: bold;">E. Received Payment:</div>
                <div style="margin-top: 10px;">
                  <div>Check No. _______________</div>
                  <div>Bank Name: _______________</div>
                  <div>ALOBS No.: _______________</div>
                </div>
                <div style="margin-top: 15px; text-align: center;">
                  <div style="font-weight: bold; text-decoration: overline;" id="voucher_payee_sig_<?php echo $la_row['id']; ?>">____________________________</div>
                  <div style="font-size: 10px;">Signature Over Printed Name/Position</div>
                  <div style="font-size: 10px;">Date__________</div>
                </div>
              </td>
            </tr>
          </table>
          
          <!-- Accounting Entries -->
          <div style="font-weight: bold; margin-top: 20px;">F. Accounting Entries</div>
          <table style="width: 100%; border-collapse: collapse;">
            <thead>
              <tr>
                <th style="border: 1px solid #000; padding: 5px; text-align: left;">Particulars</th>
                <th style="border: 1px solid #000; padding: 5px;">Account Code</th>
                <th style="border: 1px solid #000; padding: 5px;">Debit</th>
                <th style="border: 1px solid #000; padding: 5px;">Credit</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="border: 1px solid #000; padding: 5px;" id="voucher_acct_particulars_<?php echo $la_row['id']; ?>"></td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;" id="voucher_acct_code_<?php echo $la_row['id']; ?>"></td>
                <td style="border: 1px solid #000; padding: 5px; text-align: right;" id="voucher_acct_debit_<?php echo $la_row['id']; ?>"></td>
                <td style="border: 1px solid #000; padding: 5px; text-align: right;" id="voucher_acct_credit_<?php echo $la_row['id']; ?>"></td>
              </tr>
              <tr>
                <td style="border: 1px solid #000; padding: 5px;" colspan="4">
                  <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                    <div>
                      <div>Prepared by: _______________</div>
                      <div style="margin-left: 60px; font-size: 10px;">Accounting Personnel</div>
                    </div>
                    <div>
                      <div>Certified Correct:</div>
                      <div style="text-align: center; margin-top: 5px;">
                        <div>_______________</div>
                        <div style="font-size: 10px;">Head, Accounting Division/Unit</div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary">
          <i class="fa fa-times"></i> Close
        </button>
        <button type="button" onclick="printMonetizedVoucher(<?php echo $la_row['id']; ?>)" class="btn btn-primary">
          <i class="fa fa-print"></i> Print
        </button>
      </div>
    </div>
  </div>
</div>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  #print_monetized_voucher_content_<?php echo $la_row['id']; ?>, 
  #print_monetized_voucher_content_<?php echo $la_row['id']; ?> * {
    visibility: visible;
  }
  #print_monetized_voucher_content_<?php echo $la_row['id']; ?> {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .modal-footer, .modal-header {
    display: none !important;
  }
  
  /* Flexible page size - auto-adjusts to printer's paper size */
  /* Works with Letter (8.5"x11"), Folio (8.5"x13"), or Legal (8.5"x14") */
  @page {
    size: auto;
    margin: 0.5in 0.5in;
  }
  
  .voucher-container {
    max-width: 7.5in;
    margin: 0 auto;
    page-break-inside: avoid;
  }
}

.voucher-container {
  font-family: Arial, sans-serif;
  font-size: 11px;
  line-height: 1.4;
  color: #000;
  background: white;
  padding: 20px;
  max-width: 7.5in;
  margin: 0 auto;
  box-sizing: border-box;
}

@media screen {
  .voucher-container {
    max-width: 100%;
  }
}

.voucher-container table {
  width: 100%;
  table-layout: fixed;
}

.voucher-container td, .voucher-container th {
  word-wrap: break-word;
  overflow-wrap: break-word;
}
</style>
