SELECT pp.bulan, pp.tahun, AVG(g.gaji_bersih) AS rata_rata_gaji_bersih
FROM gaji g
JOIN priode_payroll pp ON g.payroll_period_id = pp.id
WHERE pp.bulan = 7 AND pp.tahun = 2026
GROUP BY pp.bulan, pp.tahun;