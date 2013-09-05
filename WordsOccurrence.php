<?php
/**
 * Вычисление частоты вхожения слов в текст с учетом морфологии
 * Используется библиотека PhpMorphy
 *
 * @author Zorin Kirill <zarincheg@gmail.com>
 * @copyright 2012 SeoStopol
 */
class WordsOccurrence {

	private $morphy = false;

	public function __construct() {
		$options = array('storage' => \PHPMORPHY_STORAGE_FILE,
		                 'predict_by_suffix' => true,
		                 'predict_by_db' => true,
		                 'graminfo_as_text' => true);

		$dictdir = \Configure::$get->path->root. 'lib/phpmorphy/dicts';

		if(!file_exists($dictdir))
			throw new \Exception('Dictonaries for PhpMorphy not found! Directory does not exists!');

		$this->morphy = new \phpMorphy($dictdir, 'ru_RU', $options);
	}

	/**
	 * Группирует слова в тексте по словоформам.
	 * <b>Замечание</b>: При поиске словоформ использует первую найденную
	 * @todo Возможно переименовать в group
	 *
	 * @param string $text Анализируемый текст
	 *
	 * @return array Ассоциативный массив, где ключи это базовые словоформы, а значение - список слов из текста
	 */
	public function occures($text) {
		$occurrences = array();
		$canonization = new \Canonization();
		$text = $canonization->allText($text);

		$words = explode(' ', $text);

		foreach($words as $word) {
			$base = $this->morphy->getBaseForm(mb_strtoupper($word, 'UTF-8'))[0];

			if(!$base) {
				$occurrences[$word][] = $word;
			} else {
				$occurrences[mb_strtolower($base, 'UTF-8')][] = $word;
			}
		}

		return $occurrences;
	}

	/**
	 * Производит подсчет кол-ва вхождений слова в текст
	 *
	 * @param $word Искомое слово
	 * @param $text Текст, в котором производится поиск
	 *
	 * @return int Кол-во вхождений
	 */
	public function occurreWord($word, $text) {
		$base = $this->morphy->getBaseForm(mb_strtoupper($word, 'UTF-8'))[0];
		$base = mb_strtolower($base, 'UTF-8');
		$f = $this->frequency($text);

		return isset($f['occures'][$base]) ? (int)$f['occures'][$base] : 0;
	}

	/**
	 * @param $text Анализируемый текст
	 *
	 * @return array Ассоциатиынй массив.
	 * <i>count</i> - кол-во слов в тексте
	 * <i>occures</i> - Массив из пар словоформа - количество вхождений
	 * <i>t</i> - "Тошнота" текста
	 */
	public function frequency($text) {
		if(!$text) return false;

		$count = 0;
		$occures = $this->occures($text);

		foreach($occures as &$list) {
			$list = count($list);
			$count += $list;
		}

		uasort($occures, function ($a, $b) {
			if($a == $b) {
				return 0;
			}

			return ($a > $b) ? -1 : 1;
		});

		return array('count' => $count,
		             'occures' => $occures,
		             't' => sqrt(array_shift($occures)));
	}
}