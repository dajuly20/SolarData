SELECT id, curtime as mynow,
        
       
       round(min(current)/10,2) as 'min current', 
	   round(avg(current)/10,2) as 'avg current',
       round(max(current)/10,2) as 'max current', 
       
       round(min(power)/1000,2) as 'min power',
       round(avg(power)/1000,2) as 'avg power', 
       round(max(power)/1000,2) as 'max power',
       
       charge/1000 as 'charge',
       round((min(charge) - max(charge))/1000,2) as chargedThisHour
       
      FROM SolarMeasurement.MeasurementData WHERE DAY(curtime) = day(now()) GROUP BY HOUR(curtime) ORDER BY mynow desc