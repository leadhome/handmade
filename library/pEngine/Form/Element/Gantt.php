<?php
class pEngine_Form_Element_Gantt extends Zend_Form_Element
{
	protected $data_js;
	protected $data_array;
	protected $date_format = 'YYYY-MM-dd H:m:s';
	protected $date_format_gantt = 'YYYY,MM,dd';
	protected $color_past = '#e0e0e0';
	protected $color_now = '#ccffcc';
	protected $color_future = '#f0f0f0';
	protected $width = 620;


	/**
	 * Устанавливаем данные для графика.
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->data_array = $data;
		$this->data_js = $this->convertData($data);

	}

	/**
	 * Устанавливает длину графика в пикселях.
	 *
	 * @param int $width
	 * @return int
	 */
	public function setWidth($width)
	{
		$this->width = $width;
		return $this->width;
	}

	/**
	 * Конвертирование даты для графика.
	 *
	 *
	 * @param array $data
	 */
	protected function convertData($data)
	{
		if(!count($data)){
			return '[]';
		}
		
		$string = '[';

		foreach($data as $key => $value){
			$date_start = new Zend_Date($value['series']['date_start'], $this->date_format);
			$date_end = new Zend_Date($value['series']['date_end'], $this->date_format);

			$string .=	'{id: ' . $value['id'] .
						', name: "' . $value['name'] .
			'", series: [{ name: "' . $value['id'] . '", start: new Date(' . $this->convertDateToJs($date_start) .
			'), end: new Date(' . $this->convertDateToJs($date_end) . ')' .
			$this->getColor($date_start, $date_end) .' }]
				},';
		}

		$string = substr($string, 0, strlen($string)-1) . '];';

		return $string;
	}

	/**
	 * Перехуриватель нормальных месяцов в наркоманские.
	 *
	 * @param Zend_Date $date нормальная дата
	 * @param string $js_date наркоманская дата
	 */
	protected function convertDateToJs($date)
	{
		$day = $date->getDay()->toString("dd");
		$month = (int)$date->getMonth()->toString("M") - 1;
		$year = $date->getYear()->toString("YYYY") + 1;

		$js_date = $year . ',0' . $month . ',' . $day;

//		var_dump($js_date);
//		die();

		return $js_date;
	}

	/**
	 * Получить цвет трэка.
	 *
	 * @param Zend_Date $date_start
	 * @param Zend_Date $date_end
	 * @return color
	 */
	protected function getColor($date_start, $date_end)
	{
		$now = new Zend_Date();

		if($now->getTimestamp() > $date_start->getTimestamp()){
			if($now->getTimestamp() > $date_end->getTimestamp()){
				return ', color: "' . $this->color_past . '"';
			}
		}else{
			if($now->getTimestamp() < $date_end->getTimestamp()){
				return  ', color: "' . $this->color_future . '"';
			}
		}

		return  ', color: "' . $this->color_now . '"';
	}

	/**
	 * Отрисовать данные для графика.
	 *
	 * @return string;
	 */
	public function renderData()
	{
		$string = '<script type="text/javascript">';
		$string .= 'var ' . $this->getName() . 'Data = ' . $this->data_js;
		$string .= '</script>';

		return $string;
	}

	protected function renderCode()
	{
		$code = '
			<script type="text/javascript">
				jQuery(function () {
					jQuery("#' . $this->getName() . 'Chart").ganttView({
						data: ' . $this->getName() . 'Data,
						slideWidth: '.$this->width.',
						behavior: {
							clickable: true,
							draggable: true,
							resizable: true,
							onClick: function (data) {
//								var msg = "You clicked on an event: { start: " + data.start.toString("yyyy-M-d") + ", end: " + data.end.toString("yyyy-M-d") + " }";
//								jQuery("#'.$this->getName().'").val(msg);
							},
							onResize: function (data) {
								var msg = "{\"id\" : \"" + data.name.toString() + "\" , \"start\": \"" + data.start.toString("yyyy-M-d") + "\", \"end\": \"" + data.end.toString("yyyy-M-d") + "\" },";
								jQuery("#'.$this->getName().'").val(jQuery("#'.$this->getName().'").val()+msg);
							},
							onDrag: function (data) {
								var msg = "{\"id\" : \"" + data.name.toString() + "\" , \"start\": \"" + data.start.toString("yyyy-M-d") + "\", \"end\": \"" + data.end.toString("yyyy-M-d") + "\" },";
								jQuery("#'.$this->getName().'").val(jQuery("#'.$this->getName().'").val()+msg);
							}
						}
					});

					// jQuery("#' . $this->getName() . 'Chart").ganttView("setSlideWidth", 600);
				});
			</script>';

		return $code;
	}

	/**
	 * Отрисуем.
	 *
	 * @param Zend_View_Interface $view
	 */
	public function render(Zend_View_Interface $view = null)
	{
		$string = '<div id="' . $this->getName() . 'Element">';
			$string .= $this->renderData();
			$string .= $this->renderCode();
		$string .= '</div>';

		$string .= '<div id="' . $this->getName() . 'Chart"></div>';
		$string .= '<input type="hidden" id="'.$this->getName().'" name="'.$this->getName().'">';

		return $string;
	}
}
