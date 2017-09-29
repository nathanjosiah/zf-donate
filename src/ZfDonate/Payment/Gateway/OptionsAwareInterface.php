<?php

namespace ZfDonate\Payment\Gateway;

interface OptionsAwareInterface {
	public function setOptions(array $options) : void;
}

