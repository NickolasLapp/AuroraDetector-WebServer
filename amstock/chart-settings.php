<?php
	//$date = $_REQUEST['date'];
	$date = '2010-09-17';
	
	include_once 'constants.php';
	
	$aurora_data = get_data($date);
	
	$dom=new DOMDocument('1.0','UTF-8');
	
	$settings = $dom->createElement('settings');
	$settingsNode = $dom->appendChild($settings);
	
		$addTimeStamp = $dom->createElement('add_time_stamp', 'true');
		$addTimeStampNode = $settings->appendChild($addTimeStamp);
		
		$maxSeries = $dom->createElement('max_series', '144');
		$maxSeriesNode = $settings->appendChild($maxSeries);
		
		$equalSpacing = $dom->createElement('equal_spacing', 'false');
		$equalSpacingNode = $settings->appendChild($equalSpacing);
		
		$formatNumber = $dom->createElement('number_format');
		$formatNumberNode = $settings->appendChild($formatNumber);
		
			$decimalSeparator = $dom->createElement('decimal_separator', '.');
			$decimalSeparatorNode = $formatNumber->appendChild($decimalSeparator);
			
			$thousandsSeparator = $dom->createElement('thousand_separator', 'none');
			$thousandsSeparatorNode = $formatNumber->appendChild($thousandsSeparator);
		
		$datasets = $dom->createElement('data_sets');
		$datasetsNode = $settings->appendChild($datasets);
		
			$dataset = $dom->createElement('data_set');
			$datasetNode = $datasets->appendChild($dataset);
			$datasetNode->setAttribute('did', '0');
		
			$color = $dom->createElement('color', '006699');
			$colorNode = $dataset->appendChild($color);
			
			$reverse = $dom->createElement('reverse', 'true');
			$reverseNode = $dataset->appendChild($reverse);
			
			$separator = $dom->createElement('separator', ',');
			$separatorNode = $dataset->appendChild($separator);
			
			$skipFirstRows = $dom->createElement('skip_first_rows', '1');
			$skipFirstRowsNode = $dataset->appendChild($skipFirstRows);
			
			$dateFormat = $dom->createElement('date_format', 'YYYY-MM-DD hh:mm');
			$dateFormatNode = $dataset->appendChild($dateFormat);
		
			$columns = $dom->createElement('columns');
			$columnsNode = $dataset->appendChild($columns);
			
			foreach($aurora_data['columns'] as $heading) {
				$column = $dom->createElement('column', $heading);
				$columnNode = $columns->appendChild($column);				
			}
			
			$csv = $dom->createElement('csv');
			$csvNode = $dataset->appendChild($csv);
						
			$data = $dom->createElement('data', $aurora_data['data']);
			$dataNode = $dataset->appendChild($data);
		
		$charts = $dom->createElement('charts');
		$chartsNode = $settings->appendChild($charts);
			
			$chart_counter = 0;
			$total_charts = (count($aurora_data['charts'])-1);
			foreach ($aurora_data['charts'] as $needed_chart) {
				$chart = $dom->createElement('chart');
				$chartNode = $charts->appendChild($chart);
				$chartNode->setAttribute('cid', $chart_counter);
				
				$chartTitle = $dom->createElement('title', $needed_chart['title']);
				$chartTitleNode = $chart->appendChild($chartTitle);
				
				$border = $dom->createElement('border_color', '006699');
				$borderNode = $chart->appendChild($border);
				
				$grid = $dom->createElement('grid');
				$gridNode = $chart->appendChild($grid);
				
					$x = $dom->createElement('x');
					$xNode = $grid->appendChild($x);
					
					$yLeft = $dom->createElement('y_left');
					$yLeftNode = $grid->appendChild($yLeft);
					
					$yRight = $dom->createElement('y_right');
					$yRightNode = $grid->appendChild($yRight);
					
				$values = $dom->createElement('values');
				$valuesNode = $chart->appendChild($values);
				
					$x = $dom->createElement('x');
					$xNode = $values->appendChild($x);
						
						if ($chart_counter < $total_charts) {
							$xEnabled = $dom->createElement('enabled', 'false');	
						} else {
							$xEnabled = $dom->createElement('enabled', 'true');
						}
						$xEnabledNode = $x->appendChild($xEnabled);
					
					$yLeft = $dom->createElement('y_left');
					$yLeftNode = $values->appendChild($yLeft);
					
					$yRight = $dom->createElement('y_right');
					$yRightNode = $values->appendChild($yRight);
					
						$yRightEnabled = $dom->createElement('enabled', 'true');
						$yRightEnabledNode = $yRight->appendChild($yRightEnabled);
					
				$legend = $dom->createElement('legend');
				$legendNode = $chart->appendChild($legend);
				
					$hideDate = $dom->createElement('hide_date_on_period', 'true');
					$hideDateNode = $legend->appendChild($hideDate);
					
					$unitPosition = $dom->createElement('unit_position', 'right');
					$unitPositionNode = $legend->appendChild($unitPosition);
					
					$fade = $dom->createElement('fade_others_to', '20');
					$fadeNode = $legend->appendChild($fade);
					
				$columnWidth = $dom->createElement('column_width', '100');
				$columnWidthNode = $chart->appendChild($columnWidth);
					
				$graphs = $dom->createElement('graphs');
				$graphsNode = $chart->appendChild($graphs);
				
				$graph_counter = 0;
				$total_graphs = (count($needed_chart['columns'])-1);
				foreach ($needed_chart['columns'] as $current_graph) {
					$graph = $dom->createElement('graph');
					$graphNode = $graphs->appendChild($graph);
					$graphNode->setAttribute('gid', $graph_counter);
					
					$axis = $dom->createElement('axis', 'right');
					$axisNode = $graph->appendChild($axis);
					
					$type = $dom->createElement('type', 'line');
					$typeNode = $graph->appendChild($type);
					
					$connect = $dom->createElement('connect', 'false');
					$connectNode = $graph->appendChild($connect);
					
					$color = $dom->createElement('color', $needed_chart['colors'][$graph_counter]);
					$colorNode = $graph->appendChild($color);
					
					$dataSources = $dom->createElement('data_sources');
					$dataSourcesNode = $graph->appendChild($dataSources);
					
						$close = $dom->createElement('close', $current_graph);
						$closeNode = $dataSources->appendChild($close);
					
					$legend = $dom->createElement('legend');
					$legendNode = $graph->appendChild($legend);
					
						$date = $dom->createElement('date', '{close}');
						$dateNode = $legend->appendChild($date);
						$dateNode->setAttribute('key', 'true');	
						$dateNode->setAttribute('title', 'true');
						
						$period = $dom->createElement('period', '{close}');
						$periodNode = $legend->appendChild($period);
						$periodNode->setAttribute('key', 'true');	
						$periodNode->setAttribute('title', 'true');
					
					$hidden = $dom->createElement('hidden', 'false');
					$hiddenNode = $graph->appendChild($hidden);
					
					$graph_counter++;
				}
					
				$chart_counter++;
			}
		
		$dataSelector = $dom->createElement('data_set_selector');
		$dataSelectorNode = $settings->appendChild($dataSelector);
			
			$enabled = $dom->createElement('enabled', 'false');
			$enabledNode = $dataSelector->appendChild($enabled);
			
		$periodSelector = $dom->createElement('period_selector');
		$periodSelectorNode = $settings->appendChild($periodSelector);
		
			$customPeriodEnabled = $dom->createElement('custom_period_enabled', 'false');
			$customPeriodEnabledNode = $periodSelector->appendChild($customPeriodEnabled);
			
			$periodsTitle = $dom->createElement('periods_title', 'Zoom:');
			$periodsTitleNode = $periodSelector->appendChild($periodsTitle);
			
			$periods = $dom->createElement('periods');
			$periodsNode = $periodSelector->appendChild($periods);
			
				$period = $dom->createElement('period', '1 Hour');
				$periodNode = $periods->appendChild($period);
				$periodNode->setAttribute('pid', '0');
				$periodNode->setAttribute('type', 'hh');
				$periodNode->setAttribute('count', '1');
				
				$period = $dom->createElement('period', '6 Hours');
				$periodNode = $periods->appendChild($period);
				$periodNode->setAttribute('pid', '1');
				$periodNode->setAttribute('type', 'hh');
				$periodNode->setAttribute('count', '6');
				
				$period = $dom->createElement('period', '12 Hours');
				$periodNode = $periods->appendChild($period);
				$periodNode->setAttribute('pid', '2');
				$periodNode->setAttribute('type', 'hh');
				$periodNode->setAttribute('count', '12');
				
				$period = $dom->createElement('period', '1 Day');
				$periodNode = $periods->appendChild($period);
				$periodNode->setAttribute('pid', '3');
				$periodNode->setAttribute('type', 'DD');
				$periodNode->setAttribute('count', '1');
				
		$header = $dom->createElement('header');
		$headerNode = $settings->appendChild($header);
		
			$enabled = $dom->createElement('enabled', 'false');
			$enabledNode = $header->appendChild($enabled);
			
		$background = $dom->createElement('background');
		$backgroundNode = $settings->appendChild($background);
		
		$plotArea = $dom->createElement('plot_area');
		$plotAreaNode = $settings->appendChild($plotArea);
		
			$margins = $dom->createElement('margins', '10');
			$marginsNode = $plotArea->appendChild($margins);
			
			$bgColor = $dom->createElement('bg_color', 'ffffff');
			$bgColorNode = $plotArea->appendChild($bgColor);
			
			$bgAlpha = $dom->createElement('bg_alpha', '100');
			$bgAlphaNode = $plotArea->appendChild($bgAlpha);
			
			$borderColor = $dom->createElement('border_color', 'ffffff');
			$borderColorNode = $plotArea->appendChild($borderColor);
			
			$borderAlpha = $dom->createElement('border_alpha', '100');
			$borderAlphaNode = $plotArea->appendChild($borderAlpha);
			
			$borderWidth = $dom->createElement('border_width', '0');
			$borderWidthNode = $plotArea->appendChild($borderWidth);
			
			$cornerRadius = $dom->createElement('corner_radius', '0');
			$cornerRadiusNode = $plotArea->appendChild($cornerRadius);
		
		$scroller = $dom->createElement('scroller');
		$scrollerNode = $settings->appendChild($scroller);
		
			$enabled = $dom->createElement('enabled', 'false');
			$enabledNode = $scroller->appendChild($enabled);
			
			//$height = $dom->createElement('height', '30');
			//$heightNode = $scroller->appendChild($height);
		
		$contextMenu = $dom->createElement('context_menu');
		$contextMenuNode = $settings->appendChild($contextMenu);
	
	//Tell the browser the output is XML via the 'Content-Type' HTTP header
	header('Content-Type: text/xml');
	
	//Display DOM document
	echo $dom->saveXML();
?>