<?php

/* ***** BEGIN LICENSE BLOCK *****
 *  
 * This file is part of FirePHP (http://www.firephp.org/).
 * 
 * Copyright (C) 2007 Christoph Dorn
 * 
 * FirePHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * FirePHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with FirePHP.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * ***** END LICENSE BLOCK ***** */


/*
 * @copyright  Copyright (C) 2007 Christoph Dorn
 * @license    http://www.gnu.org/licenses/lgpl.html
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */
 
class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        Zend_Registry::get('logger')->err($errors->exception);
    }
}

