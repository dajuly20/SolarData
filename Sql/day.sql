SELECT id, curtime as mynow,
	   DATE_FORMAT(curtime, '%H%:00') as date,
       round(min(current)/10,2) as 'min current', 
	   round(avg(current)/10,2) as 'avg current',
       round(max(current)/10,2) as 'max current', 
       round(min(power)/1000,2) as 'min power',
       round(avg(power)/1000,2) as 'avg power', 
       round(max(power)/1000,2) as 'max power',
       charge/1000 as 'charge',
       round((min(charge) - max(charge))/1000,2) as chargedThisHour,
       round((((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id) - charge)/1000),2) as chgDiff,
       
      FROM MeasurementData.SolarPower WHERE DAY(curtime) = day('2018-05-05 12:00:00') 
      GROUP BY HOUR(curtime) ORDER BY mynow desc
