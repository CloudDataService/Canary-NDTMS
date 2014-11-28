<div class="header">
    <h2>Modalities</h2>
</div>
<div class="item results">

    <?php if(isset($journey['modalities']) && $journey['modalities']) : ?>
        <table class="results vat">

            <tr class="order">
                <th>Care Plan Date</th>
                <th>Treatment</th>
                <th>Date Referred</th>
                <th>Date of First Appointment Offered</th>
                <th>Intervention Setting</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Exit Status</th>

                <th>Edit</th>
                <?php if($this->session->userdata('a_master')) : // if master admin is logged in ?>
                    <th>Delete</th>
                <?php endif; ?>
            </tr>

            <?php foreach($journey['modalities'] as $mod) : ?>
            <tr class="row no_click">
                <td><?php echo $mod['CPLANDT']; ?></td>
                <td><?php echo element($mod['MODAL'], $modality_treatments); ?></td>
                <td><?php echo $mod['REFMODDT']; ?></td>
                <td><?php echo $mod['FAOMODDT']; ?></td>
                <td><?php echo element($mod['MODSET'], $intervention_setting); ?></td>
                <td><?php echo $mod['MODST']; ?></td>
                <td><?php echo $mod['MODEND']; ?></td>
                <td><?php echo element($mod['MODEXIT'], $exit_status); ?></td>

                <td class="actionno_click"><a href="/admin/ajax/modality/<?php echo $journey['j_id'] . '/' . $mod['MODID']; ?>" class="modality_btn"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
                <?php if($this->session->userdata('a_master')) : // if master admin is logged in ?>
                    <td class="action"><a href="?mod_id=<?php echo $mod['MODID']; ?>&amp;delete=1" class="action" title="Are you sure you want to delete this modality?"><img src="/img/icons/cross.png" alt="Delete" /></a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>

        </table>
    <?php else : ?>
        <p class="no_results">There have been no modalities recorded for this journey.</p>
    <?php endif; ?>

</div>
<p class="back_to_top">[<a href="#top">back to top</a>]</p>
