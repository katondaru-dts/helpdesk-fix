<?= $this->extend("layouts/main") ?>
<?= $this->section("content") ?>
<div class="page-header"><div><div class="page-header-title">Audit Logs (Administrator)</div><div class="page-header-sub">Catatan aktivitas dan perubahan sistem</div></div></div>
<div class="card mb-4" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);">
<div style="overflow-x:auto;">
<table class="table" style="width:100%;border-collapse:collapse;min-width:800px;">
<thead><tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
<th style="padding:15px;text-align:left;color:#6b7280;font-size:12px;text-transform:uppercase;">Tanggal</th>
<th style="padding:15px;text-align:left;color:#6b7280;font-size:12px;text-transform:uppercase;">User</th>
<th style="padding:15px;text-align:left;color:#6b7280;font-size:12px;text-transform:uppercase;">IP</th>
<th style="padding:15px;text-align:left;color:#6b7280;font-size:12px;text-transform:uppercase;">Aksi</th>
<th style="padding:15px;text-align:left;color:#6b7280;font-size:12px;text-transform:uppercase;">Target</th>
<th style="padding:15px;text-align:left;color:#6b7280;font-size:12px;text-transform:uppercase;">Detail Perubahan</th>
</tr></thead>
<tbody>
<?php if (empty($logs)): ?>
<tr><td colspan="6" style="padding:20px;text-align:center;color:#6b7280;">Belum ada log.</td></tr>
<?php else: ?>
<?php foreach ($logs as $log): ?>
<tr style="border-bottom:1px solid #f3f4f6;">
<td style="padding:15px;font-size:13px;color:#4b5563;"><?= date("d M Y H:i:s", strtotime($log["created_at"])) ?></td>
<td style="padding:15px;font-size:13px;font-weight:600;color:#111827;"><?= esc($log["user_name"]) ?><br><span style="font-size:11px;font-weight:normal;color:#6b7280;"><?= esc($log["user_email"]) ?></span></td>
<td style="padding:15px;font-size:13px;color:#4b5563;font-family:monospace;"><?= esc($log["ip_address"]) ?></td>
<td style="padding:15px;font-size:13px;">
<?php $color="#6b7280";$bg="#f3f4f6";if($log["action"]=="CREATE"){$color="#10b981";$bg="#d1fae5";}elseif($log["action"]=="UPDATE"){$color="#3b82f6";$bg="#eff6ff";}elseif($log["action"]=="DELETE"){$color="#ef4444";$bg="#fee2e2";}elseif($log["action"]=="TOGGLE_STATUS"){$color="#f59e0b";$bg="#fef3c7";} ?>
<span style="background:<?=$bg?>;color:<?=$color?>;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:bold;"><?= esc($log["action"]) ?></span>
</td>
<td style="padding:15px;">
<span style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:bold;text-transform:uppercase;"><i class="bi bi-database" style="margin-right:4px;"></i><?= esc($log["target_table"]) ?></span>
<?php if(!empty($log["target_id"])): ?><span style="background:#fefce8;border:1px solid #fef08a;color:#854d0e;padding:4px 6px;border-radius:6px;font-size:11px;margin-left:4px;font-weight:bold;">ID: <?= esc($log["target_id"]) ?></span><?php endif; ?>
</td>
<td style="padding:15px;font-size:12px;max-width:400px;">
<?php $details=$log["details"]??"";$parsed=@json_decode($details,true);if(is_array($parsed)&&!empty($parsed)): ?>
<div style="display:flex;flex-wrap:wrap;gap:5px;">
<?php foreach($parsed as $key=>$val):if(in_array(strtolower($key),["password","pwd","token","remember_token"])){$val="********";}elseif(is_array($val)){$val=json_encode($val);}elseif($val===null||$val===""){$val="(kosong)";} ?>
<div style="background:#f9fafb;border:1px solid #e5e7eb;padding:3px 8px;border-radius:6px;font-size:11px;display:inline-flex;align-items:center;gap:5px;">
<span style="color:#9ca3af;font-weight:700;font-size:9px;text-transform:uppercase;"><?= esc($key) ?></span>
<span style="color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;" title="<?= esc($val) ?>"><?= esc($val) ?></span>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<span style="color:#9ca3af;font-style:italic;"><?= esc($details)?:"-" ?></span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table></div>
<?php if(!empty($pager_links)): ?><div style="padding:15px 20px;border-top:1px solid #e5e7eb;"><?= $pager_links ?></div><?php endif; ?>
</div>
<?= $this->endSection() ?>