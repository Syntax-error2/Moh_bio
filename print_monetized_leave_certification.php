<!-- PRINT CERTIFICATION ON APPROPRIATIONS, FUNDS AND OBLIGATION OF ALLOTMENT -->
<div id="print_monetized_cert_<?php echo $la_row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="printMonetizedCertLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-xl" style="max-width: 1200px;">
    <div class="modal-content">
    
      <div class="modal-header bg-info text-white">
        <h5 id="printMonetizedCertLabel" class="modal-title">
          <i class="fa fa-print"></i> CERTIFICATION ON APPROPRIATIONS, FUNDS AND OBLIGATION OF ALLOTMENT
        </h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close text-white">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body p-4" id="print_monetized_cert_content_<?php echo $la_row['id']; ?>" style="max-height: 80vh; overflow-y: auto;">
        <div class="cert-container">
          
          <!-- Header -->
          <div class="cert-header text-center">
            <?php if (!empty($sf_row['logo']) && file_exists('img/' . $sf_row['logo'])): ?>
              <img src="img/<?php echo htmlspecialchars($sf_row['logo']); ?>" alt="Logo" style="width: 80px; height: 80px; margin: 0 auto 10px; display: block;">
            <?php else: ?>
              <div class="seal-placeholder" style="width: 80px; height: 80px; border: 2px solid #000; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center;">
                <small>SEAL</small>
              </div>
            <?php endif; ?>
            <div style="font-size: 11px; margin-bottom: 3px;">Republic of the Philippines</div>
            <div style="font-size: 11px; margin-bottom: 3px;"><?php echo htmlspecialchars($sf_row['region'] ?? 'Province of Negros Occidental'); ?></div>
            <div style="font-size: 12px; font-weight: bold; margin-bottom: 3px;"><?php echo htmlspecialchars($sf_row['institution_name'] ?? 'Municipality of Hinoba-an'); ?></div>
            <div style="font-size: 11px; font-weight: bold;">OFFICE OF THE MUNICIPAL MAYOR</div>
          </div>
          
          <!-- Title -->
          <div style="text-align: center; margin: 20px 0; font-weight: bold; font-size: 13px;">
            CERTIFICATION ON APPROPRIATIONS, FUNDS AND OBLIGATION OF ALLOTMENT
          </div>
          
          <!-- Main Content Table -->
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
              <td style="border: 1px solid #000; padding: 10px; width: 60%; vertical-align: top;">
                <div style="margin-bottom: 15px;">
                  <strong>Request:</strong> <span id="cert_request_<?php echo $la_row['id']; ?>"></span>
                </div>
                <div style="margin-bottom: 15px;">
                  <strong>Payee:</strong> <span id="cert_payee_<?php echo $la_row['id']; ?>"></span>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                  <thead>
                    <tr>
                      <th style="border: 1px solid #000; padding: 5px; text-align: center;">Function</th>
                      <th style="border: 1px solid #000; padding: 5px; text-align: center;">Allotment Class</th>
                      <th style="border: 1px solid #000; padding: 5px; text-align: center;">Expense Code</th>
                      <th style="border: 1px solid #000; padding: 5px; text-align: center;">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td style="border: 1px solid #000; padding: 5px;">MO</td>
                      <td style="border: 1px solid #000; padding: 5px;">PS</td>
                      <td style="border: 1px solid #000; padding: 5px;">Monetized</td>
                      <td style="border: 1px solid #000; padding: 5px; text-align: right;" id="cert_amount_table_<?php echo $la_row['id']; ?>"></td>
                    </tr>
                  </tbody>
                </table>
                
                <div style="margin-top: 15px;">
                  <strong>Total amount requested:</strong> <span style="text-decoration: underline;" id="cert_total_amount_<?php echo $la_row['id']; ?>"></span>
                </div>
                
                <div style="margin-top: 10px;">
                  <strong>Amount in Words:</strong> <span style="text-decoration: underline;" id="cert_amount_words_<?php echo $la_row['id']; ?>"></span>
                </div>
                
                <div style="margin-top: 20px;">
                  <div><strong>Requesting Official:</strong></div>
                  <div style="margin-top: 30px; text-align: center;">
                    <div style="font-weight: bold; text-decoration: overline;" id="cert_admin_name_<?php echo $la_row['id']; ?>">____________________________</div>
                    <div id="cert_admin_position_<?php echo $la_row['id']; ?>">Municipal Administrator</div>
                    <div style="margin-left: 100px;">Date__________</div>
                  </div>
                </div>
                
              </td>
              
              <td style="border: 1px solid #000; padding: 10px; width: 40%; vertical-align: top;">
                <div style="margin-bottom: 15px;">
                  <strong>Obligation No.:</strong> <span id="cert_obligation_no_<?php echo $la_row['id']; ?>">__________________</span>
                </div>
                <div style="margin-bottom: 15px;">
                  <strong>Approved Amount:</strong> <span style="text-decoration: underline;" id="cert_approved_amount_<?php echo $la_row['id']; ?>"></span>
                </div>
                
                <!-- Certification 1: Budget Officer -->
                <div style="margin-bottom: 20px;">
                  <div style="font-weight: bold;">Certification:</div>
                  <div style="font-size: 10px; margin: 5px 0;">I hereby certify as to the existence of appropriations for the expenditures in the amount specified herein:</div>
                  <div style="margin-top: 20px; text-align: center;">
                    <div style="font-weight: bold; text-decoration: overline;" id="cert_budget_officer_<?php echo $la_row['id']; ?>">____________________________</div>
                    <div style="font-size: 10px;" id="cert_budget_position_<?php echo $la_row['id']; ?>">Mun. Budget Officer</div>
                    <div style="font-size: 10px;">Date__________</div>
                  </div>
                </div>
                
                <!-- Certification 2: Treasurer -->
                <div style="margin-bottom: 20px;">
                  <div style="font-weight: bold;">Certification:</div>
                  <div style="font-size: 10px; margin: 5px 0;">I hereby certify as to the availability of funds for the expenditures in the amount specified herein:</div>
                  <div style="margin-top: 20px; text-align: center;">
                    <div style="font-weight: bold; text-decoration: overline;" id="cert_treasurer_<?php echo $la_row['id']; ?>">____________________________</div>
                    <div style="font-size: 10px;" id="cert_treasurer_position_<?php echo $la_row['id']; ?>">Acting Mun.-Treasurer</div>
                    <div style="font-size: 10px;">Date__________</div>
                  </div>
                </div>
                
                <!-- Certification 3: Accountant -->
                <div>
                  <div style="font-weight: bold;">Certification:</div>
                  <div style="font-size: 10px; margin: 5px 0;">I hereby certify that the allotments are available for obligation in the amount specified herein:</div>
                  <div style="margin-top: 20px; text-align: center;">
                    <div style="font-weight: bold; text-decoration: overline;" id="cert_accountant_<?php echo $la_row['id']; ?>">____________________________</div>
                    <div style="font-size: 10px;" id="cert_accountant_position_<?php echo $la_row['id']; ?>">Mun. Accountant</div>
                    <div style="font-size: 10px;">Date__________</div>
                  </div>
                </div>
                
              </td>
            </tr>
          </table>
          
          <!-- Subsidiary Ledger -->
          <div style="text-align: center; font-weight: bold; margin: 10px 0;">Subsidiary Ledger</div>
          <table style="width: 100%; border-collapse: collapse;">
            <thead>
              <tr>
                <th style="border: 1px solid #000; padding: 5px;">Date</th>
                <th style="border: 1px solid #000; padding: 5px;">Particulars /Reference</th>
                <th style="border: 1px solid #000; padding: 5px;">Liquidations</th>
                <th style="border: 1px solid #000; padding: 5px;">Obligation Increase (Decrease)</th>
                <th style="border: 1px solid #000; padding: 5px;">Balance</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
              </tr>
              <tr>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
                <td style="border: 1px solid #000; padding: 10px;">&nbsp;</td>
              </tr>
            </tbody>
          </table>
          
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary">
          <i class="fa fa-times"></i> Close
        </button>
        <button type="button" onclick="printMonetizedCertification(<?php echo $la_row['id']; ?>)" class="btn btn-info">
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
  #print_monetized_cert_content_<?php echo $la_row['id']; ?>, 
  #print_monetized_cert_content_<?php echo $la_row['id']; ?> * {
    visibility: visible;
  }
  #print_monetized_cert_content_<?php echo $la_row['id']; ?> {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .modal-footer, .modal-header {
    display: none !important;
  }
  
  /* Flexible page size - works with Letter (8.5"x11"), Legal (8.5"x14"), or Folio (8.5"x13") */
  @page {
    size: auto;
    margin: 0.5in 0.5in;
  }
  
  .cert-container {
    max-width: 7.5in;
    margin: 0 auto;
    page-break-inside: avoid;
  }
}

.cert-container {
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
  .cert-container {
    max-width: 100%;
  }
}

.cert-container table {
  width: 100%;
  table-layout: fixed;
}

.cert-container td, .cert-container th {
  word-wrap: break-word;
  overflow-wrap: break-word;
}
</style>
