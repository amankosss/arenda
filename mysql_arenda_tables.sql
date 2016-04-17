
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `CODES` (
  `INDX` int(11) NOT NULL AUTO_INCREMENT,
  `PHONE` varchar(255) NOT NULL COMMENT 'Номер телефона пользователя',
  `OLDPHONE` varchar(255) NOT NULL COMMENT 'Старый номер телефона пользователя (если это смена)',
  `CODEID` int(11) NOT NULL COMMENT 'Код подтверждения, отправленный на телефон',
  `ACTIVATED` varchar(255) NOT NULL DEFAULT 'NO' COMMENT 'Активирован ли данный код',
  `REQUESTDATE` varchar(255) NOT NULL COMMENT 'Дата запроса кода',
  `REQUESTTIME` int(11) NOT NULL COMMENT 'Дата запроса кода (unix)',
  `ACTIVATEDATE` varchar(255) NOT NULL COMMENT 'Дата активации кода',
  `ACTIVATETIME` int(11) NOT NULL COMMENT 'Дата активации кода (unix)',
  `OS` varchar(15) NOT NULL COMMENT 'iOS или Android',
  `OSVERSION` varchar(15) NOT NULL COMMENT 'Версия операционной системы',
  `DEVICE` varchar(255) NOT NULL COMMENT 'Название и модель устройства',
  `IP` varchar(255) NOT NULL COMMENT 'IP адрес',
  PRIMARY KEY (`INDX`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `USERS` (
  `INDX` int(11) NOT NULL AUTO_INCREMENT,
  `ACTIVE` varchar(15) NOT NULL DEFAULT 'YES' COMMENT 'Активен или нет (заблокирован) данный пользователь',
  `USERID` varchar(255) NOT NULL COMMENT 'Уникальный ID пользователя (у риелторов = номеру телефона)',
  `PHONE` varchar(255) NOT NULL COMMENT 'Номер телефона пользователя',
  `CODEID` int(11) NOT NULL COMMENT 'Код подтверждения, отправленный на телефон ранее',
  `MODE` varchar(15) NOT NULL COMMENT 'Режим соискателя или риелтора',
  `LATITUDE` float NOT NULL COMMENT 'Широта',
  `LONGITUDE` float NOT NULL COMMENT 'Долгота',
  `REGDATE` varchar(255) NOT NULL COMMENT 'Дата регистрации',
  `REGTIME` int(11) NOT NULL COMMENT 'Дата регистрации (unix)',
  `ENTERDATE` varchar(255) NOT NULL COMMENT 'Последний вход в приложение',
  `ENTERTIME` int(11) NOT NULL COMMENT 'Последний вход в приложение (unix)',
  `PHONECHANGEDATE` varchar(255) NOT NULL COMMENT 'Когда был изменен номер телефона',
  `PHONECHANGETIME` int(11) NOT NULL COMMENT 'Когда был изменен номер телефона (unix)',
  `STARS` int(11) NOT NULL COMMENT 'Оценка пользователя',
  `APPVERSION` varchar(15) NOT NULL COMMENT 'Версия приложения у пользователя',
  `OS` varchar(15) NOT NULL COMMENT 'iOS или Android',
  `OSVERSION` varchar(15) NOT NULL COMMENT 'Версия операционной системы',
  `DEVICE` varchar(255) NOT NULL COMMENT 'Название и модель устройства',
  `TOKEN` varchar(255) NOT NULL COMMENT 'Device Token устройства для отправки Push уведомлений пользователю',
  `NOTIFY` varchar(15) NOT NULL DEFAULT 'YES' COMMENT 'Уведомлять о новых соискателях (только для риелторов)',
  `IP` varchar(255) NOT NULL COMMENT 'IP адрес пользователя',
  `SEARCHING` varchar(255) NOT NULL DEFAULT 'NO' COMMENT 'Поиск жилья в данный момент (только для соискателей)',
  `ACTIONTIME` int(11) NOT NULL COMMENT 'Время выполнения действия (unix)',
  `SEARCHDATE` varchar(255) NOT NULL COMMENT 'Дата начала поиска жилья',
  `SEARCHTIME` int(11) NOT NULL COMMENT 'Дата начала поиска жилья (unix)',
  `SEARCHCANCELDATE` varchar(255) NOT NULL COMMENT 'Дата отмены поиска жилья',
  `SEARCHCANCELTIME` int(11) NOT NULL COMMENT 'Дата отмены поиска жилья (unix)',
  `REGION` varchar(255) NOT NULL COMMENT 'Район поиска жилья (только для соискателей)',
  `REGIONLATITUDE` float NOT NULL COMMENT 'Широта района поиска',
  `REGIONLONGITUDE` float NOT NULL COMMENT 'Долгота района поиска',
  `ROOMS` int(11) NOT NULL COMMENT 'Количество комнат (только для соискателей)',
  `RENTTIME` varchar(255) NOT NULL COMMENT 'Период проживания (только для соискателей)',
  `FLATCOST` int(11) NOT NULL COMMENT 'Максимальная цена за квартиру (только для соискателей)',
  `REALTORCOST` int(11) NOT NULL COMMENT 'Максимальная комиссия риелтору (только для соискателей)',
  `COMMENT` varchar(255) NOT NULL COMMENT 'Комментарий к поиску жилья (только для соискателей)',
  PRIMARY KEY (`INDX`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PAYMENTS` (
  `INDX` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `USERID` varchar(255) NOT NULL COMMENT 'Уникальный ID пользователя',
  `PAYMENTDATE` varchar(255) NOT NULL COMMENT 'Дата и время покупки',
  `EXPIREDATE` varchar(255) NOT NULL COMMENT 'Время окончания покупки',
  `EXPIRETIME` int(11) NOT NULL COMMENT 'Время окончания покупки (unix)',
  `DESCRIPTION` varchar(255) NOT NULL COMMENT 'Описание подписки',
  `TIMES` int(11) NOT NULL DEFAULT '1' COMMENT 'Сколько раз уже риелтор продлял подписку',
  PRIMARY KEY (`INDX`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `LOG_USERS` (
  `INDX` int(11) NOT NULL AUTO_INCREMENT,
  `USERID` varchar(255) NOT NULL COMMENT 'Уникальный ID пользователя (у риелторов = номеру телефона)',
  `PHONE` varchar(255) NOT NULL COMMENT 'Номер телефона пользователя',
  `MODE` varchar(15) NOT NULL COMMENT 'Режим соискателя или риелтора',
  `ENTERDATE` varchar(255) NOT NULL COMMENT 'Последний вход в приложение',
  `ENTERTIME` int(11) NOT NULL COMMENT 'Последний вход в приложение (unix)',
  `STARS` int(11) NOT NULL COMMENT 'Оценка пользователя',
  `OS` varchar(15) NOT NULL COMMENT 'iOS или Android',
  `OSVERSION` varchar(15) NOT NULL COMMENT 'Версия операционной системы',
  `DEVICE` varchar(255) NOT NULL COMMENT 'Название и модель устройства',
  `IP` varchar(255) NOT NULL COMMENT 'IP адрес пользователя',
  PRIMARY KEY (`INDX`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `LOG_PAYMENTS` (
  `INDX` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `USERID` varchar(255) NOT NULL COMMENT 'Уникальный ID пользователя (у риелторов = номеру телефона)',
  `PRODUCTID` varchar(255) NOT NULL COMMENT 'Какой товар хочет купить пользователь',
  `TYPE` varchar(255) NOT NULL COMMENT 'Тип операции',
  `TYPEDESC` varchar(255) NOT NULL COMMENT 'Описание операции',
  `PURCHASEDATE` varchar(255) NOT NULL COMMENT 'Время покупки',
  `PURCHASETIME` int(11) NOT NULL COMMENT 'Время покупки (unix)',
  `APPVERSION` varchar(15) NOT NULL COMMENT 'Версия приложения у пользователя',
  `OS` varchar(15) NOT NULL COMMENT 'iOS или Android',
  `OSVERSION` varchar(15) NOT NULL COMMENT 'Версия операционной системы',
  `DEVICE` varchar(255) NOT NULL COMMENT 'Название и модель устройства',
  `IP` varchar(255) NOT NULL COMMENT 'IP адрес пользователя',
  PRIMARY KEY (`INDX`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
