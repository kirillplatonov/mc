<?php
/**
	 * MobileCMS
	 *
	 * Open source content management system for mobile sites
	 *
	 * @author MobileCMS Team <support@mobilecms.pro>
	 * @copyright Copyright (c) 2011-2019, MobileCMS Team
	 * @link https://mobilecms.pro Official site
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

//---------------------------------------------

/**
 * Установщик / деинсталлятор модуля смайлов
 */
class smiles_installer {
	/**
	 * Установка модуля
	 */
	public static function install($db) {
		$db->query("CREATE TABLE #__smiles (
			  smile_id int(11) NOT NULL auto_increment,
			  code varchar(50) NOT NULL,
			  image varchar(50) NOT NULL,
			  status enum('enable','desable') NOT NULL default 'enable',
			  PRIMARY KEY  (`smile_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8 AUTO_INCREMENT=222 ;
		");

		$db->query("INSERT INTO `a_smiles` VALUES
			(1, 'O:-)', 'aa.gif', 'enable'),
			(2, 'O=)', 'aa.gif', 'enable'),
			(3, ':-)', 'ab.gif', 'enable'),
			(4, ':)', 'ab.gif', 'enable'),
			(5, '=)', 'ab.gif', 'enable'),
			(6, ':-(', 'ac.gif', 'enable'),
			(7, ':(', 'ac.gif', 'enable'),
			(8, ';(', 'ac.gif', 'enable'),
			(9, ';-)', 'ad.gif', 'enable'),
			(10, ';)', 'ad.gif', 'enable'),
			(11, ':-P', 'ae.gif', 'enable'),
			(12, '8-)', 'af.gif', 'enable'),
			(13, ':-D', 'ag.gif', 'enable'),
			(14, ':-[', 'ah.gif', 'enable'),
			(15, '=-O', 'ai.gif', 'enable'),
			(16, ':-*', 'aj.gif', 'enable'),
			(17, ':-X', 'al.gif', 'enable'),
			(18, ':-x', 'al.gif', 'enable'),
			(19, '>:o', 'am.gif', 'enable'),
			(20, ':-|', 'an.gif', 'enable'),
			(21, ':-/', 'ao.gif', 'enable'),
			(22, '*JOKINGLY*', 'ap.gif', 'enable'),
			(23, ']:->', 'aq.gif', 'enable'),
			(24, '[:-}', 'ar.gif', 'enable'),
			(25, '*KISSED*', 'as.gif', 'enable'),
			(26, ':-!', 'at.gif', 'enable'),
			(27, '*TIRED*', 'au.gif', 'enable'),
			(28, '*STOP*', 'av.gif', 'enable'),
			(29, '*KISSING*', 'aw.gif', 'enable'),
			(31, '*THUMBS UP*', 'ay.gif', 'enable'),
			(32, '*DRINK*', 'az.gif', 'enable'),
			(33, '*IN LOVE*', 'ba.gif', 'enable'),
			(34, '@=', 'bb.gif', 'enable'),
			(35, '*HELP*', 'bc.gif', 'enable'),
			(36, 'm/', 'bd.gif', 'enable'),
			(37, '%)', 'be.gif', 'enable'),
			(38, '*OK*', 'bf.gif', 'enable'),
			(39, '*WASSUP*', 'bg.gif', 'enable'),
			(40, '*SUP*', 'bg.gif', 'enable'),
			(41, '*SORRY*', 'bh.gif', 'enable'),
			(42, '*BRAVO*', 'bi.gif', 'enable'),
			(43, '*ROFL*', 'bj.gif', 'enable'),
			(44, '*LOL*', 'bj.gif', 'enable'),
			(45, '*PARDON*', 'bk.gif', 'enable'),
			(46, '*NO*', 'bl.gif', 'enable'),
			(47, '*CRAZY*', 'bm.gif', 'enable'),
			(48, '*DONT_KNOW*', 'bn.gif', 'enable'),
			(49, '*UNKNOWN*', 'bn.gif', 'enable'),
			(50, '*DANCE*', 'bo.gif', 'enable'),
			(51, '*YAHOO*', 'bp.gif', 'enable'),
			(52, '*YAHOO!*', 'bp.gif', 'enable'),
			(53, '*NEW_PACK*', 'bq.gif', 'enable'),
			(54, '*TEASE*', 'br.gif', 'enable'),
			(55, '*SALIVA*', 'bs.gif', 'enable'),
			(56, '*DASH*', 'bt.gif', 'enable'),
			(57, '*WILD*', 'bu.gif', 'enable'),
			(58, '*TRAINING*', 'bv.gif', 'enable'),
			(59, '*FOCUS*', 'bw.gif', 'enable'),
			(60, '*HANG*', 'bx.gif', 'enable'),
			(61, '*DANCE*', 'by.gif', 'enable'),
			(62, '*DANCE2*', 'bz.gif', 'enable'),
			(63, '*MEGA_SHOK*', 'ca.gif', 'enable'),
			(64, '*TO_PICK_ONES_NOSE*', 'cb.gif', 'enable'),
			(65, '*YU*', 'cc.gif', 'enable'),
			(66, '*HUNTER*', 'cd.gif', 'enable'),
			(67, '*KUKU*', 'ce.gif', 'enable'),
			(68, '*FUCK*', 'cf.gif', 'enable'),
			(69, '*FAN*', 'cg.gif', 'enable'),
			(70, '*ASS*', 'ch.gif', 'enable'),
			(71, '*LOCOMOTIVE*', 'ci.gif', 'enable'),
			(72, '*BB*', 'cj.gif', 'enable'),
			(73, '*CONCUSSION*', 'ck.gif', 'enable'),
			(74, '*PLEASANTRY*', 'cl.gif', 'enable'),
			(75, '*DISAPPEAR*', 'cm.gif', 'enable'),
			(76, '*SUICIDE*', 'cn.gif', 'enable'),
			(77, '*PILOT*', 'co.gif', 'enable'),
			(78, '*DOWN*', 'cp.gif', 'enable'),
			(79, '*ENERGY*', 'cq.gif', 'enable'),
			(80, '*STINKER*', 'cr.gif', 'enable'),
			(81, '*PREVED*', 'cs.gif', 'enable'),
			(82, '*I-M_SO_HAPPY*', 'ct.gif', 'enable'),
			(83, '*PRANKSTER*', 'cu.gif', 'enable'),
			(84, '*LAUGH*', 'cv.gif', 'enable'),
			(85, '*BOAST*', 'cw.gif', 'enable'),
			(86, '*THANK_YOU*', 'cx.gif', 'enable'),
			(87, '*SHOUT*', 'cy.gif', 'enable'),
			(88, '*VICTORY*', 'cz.gif', 'enable'),
			(89, '*WINK*', 'da.gif', 'enable'),
			(90, '*SPITEFUL*', 'db.gif', 'enable'),
			(91, '*BYE*', 'dc.gif', 'enable'),
			(92, '*THIS*', 'dd.gif', 'enable'),
			(93, '*DON-T_MENTION*', 'de.gif', 'enable'),
			(94, '*SARCASTIC_HAND*', 'df.gif', 'enable'),
			(95, '*FIE*', 'dg.gif', 'enable'),
			(96, '*SWOON*', 'dh.gif', 'enable'),
			(97, '*SCARE*', 'di.gif', 'enable'),
			(98, '*ANGER*', 'dj.gif', 'enable'),
			(99, '*YESS*', 'dk.gif', 'enable'),
			(100, '*VAVA*', 'dl.gif', 'enable'),
			(101, '*SCRATCH_ONE-S_HEAD*', 'dm.gif', 'enable'),
			(102, '*NONO*', 'dn.gif', 'enable'),
			(103, '*WHISTLE*', 'do.gif', 'enable'),
			(104, '*UMNIK*', 'dp.gif', 'enable'),
			(105, '*ZOOM*', 'dq.gif', 'enable'),
			(106, '*HEAT*', 'dr.gif', 'enable'),
			(107, '*DECLARE*', 'ds.gif', 'enable'),
			(108, '*IDEA*', 'dt.gif', 'enable'),
			(109, '*ON_THE_QUIET*', 'du.gif', 'enable'),
			(110, '*GIVE_HEART*', 'dv.gif', 'enable'),
			(111, '*GIVE_FLOWERS*', 'dw.gif', 'enable'),
			(112, '*FRIENDS*', 'dx.gif', 'enable'),
			(113, '*PUNISH*', 'dy.gif', 'enable'),
			(114, '*PORKA*', 'dz.gif', 'enable'),
			(115, '*PARTY*', 'ea.gif', 'enable'),
			(116, '*GIRL_SMILE*', 'eb.gif', 'enable'),
			(117, '*TENDER*', 'ec.gif', 'enable'),
			(119, '*CURTSEY*', 'ee.gif', 'enable'),
			(120, '*GOGOT*', 'ef.gif', 'enable'),
			(121, '*GIRL_WINK*', 'eg.gif', 'enable'),
			(122, '*GIRL_BLUM*', 'eh.gif', 'enable'),
			(123, '*GIRL_HIDE*', 'ei.gif', 'enable'),
			(124, '*GIRL_CRAZY*', 'ej.gif', 'enable'),
			(125, '*GIRL_WACKO*', 'ek.gif', 'enable'),
			(126, '*GIRL_IN_LOVE*', 'el.gif', 'enable'),
			(127, '*GIRL_DANCE*', 'em.gif', 'enable'),
			(128, '*KISS2*', 'en.gif', 'enable'),
			(129, '*GIRL_PINKGLASSESF*', 'eo.gif', 'enable'),
			(130, '*GIRL_MAD*', 'ep.gif', 'enable'),
			(131, '*HISTERIC*', 'eq.gif', 'enable'),
			(132, '*GIRL_SIGH*', 'er.gif', 'enable'),
			(133, '*GIRL_SAD*', 'es.gif', 'enable'),
			(134, '*GIRL_CRAY*', 'et.gif', 'enable'),
			(135, '*GIRL_CRAY2*', 'eu.gif', 'enable'),
			(136, '*GIRL_IMPOSSIBLE*', 'ev.gif', 'enable'),
			(137, '*GIRL_DRINK*', 'ew.gif', 'enable'),
			(138, '*GIRL_MIRROR*', 'ex.gif', 'enable'),
			(139, '*NAILS*', 'ey.gif', 'enable'),
			(140, '*GIRL_HOSPITAL*', 'ez.gif', 'enable'),
			(141, '*GIRL_KID*', 'fa.gif', 'enable'),
			(142, '*GIRL_HAIR_DRIER*', 'fb.gif', 'enable'),
			(143, '*GIRL_WITCH*', 'fc.gif', 'enable'),
			(144, '*FIRST_MOVIE*', 'fd.gif', 'enable'),
			(145, '*SLAP_IN_THE_FACE*', 'fe.gif', 'enable'),
			(146, '*FRIENDSHIP*', 'ff.gif', 'enable'),
			(147, '*GIRL_KISSES*', 'fg.gif', 'enable'),
			(148, '*ON_HANDS*', 'fh.gif', 'enable'),
			(149, '*IT_IS_LOVE*', 'fi.gif', 'enable'),
			(150, '*SUPPER_FOR_A_TWO*', 'fj.gif', 'enable'),
			(151, '*SEX_BEHIND*', 'fk.gif', 'enable'),
			(152, '*SEX_BED*', 'fl.gif', 'enable'),
			(153, '*BABY1*', 'fm.gif', 'enable'),
			(154, '*BABY2*', 'fn.gif', 'enable'),
			(155, '*BABY3*', 'fo.gif', 'enable'),
			(156, '*BABY4*', 'fp.gif', 'enable'),
			(157, '*BABY5*', 'fq.gif', 'enable'),
			(158, '*MUSIC_FORGE*', 'fr.gif', 'enable'),
			(159, '*MUSIC_SAXOPHONE*', 'fs.gif', 'enable'),
			(160, '*MUSIC_FLUTE*', 'ft.gif', 'enable'),
			(161, '*MUSIC_VIOLIN*', 'fu.gif', 'enable'),
			(162, '*MUSIC_PIANO*', 'fv.gif', 'enable'),
			(163, '*MUSIC_DRUMS*', 'fw.gif', 'enable'),
			(164, '*MUSIC_ACCORDION*', 'fx.gif', 'enable'),
			(165, '*VINSENT*', 'fy.gif', 'enable'),
			(166, '*FRENK*', 'fz.gif', 'enable'),
			(167, '*TOMMY*', 'ga.gif', 'enable'),
			(168, '*BIG_BOSS*', 'gb.gif', 'enable'),
			(169, '*HI*', 'gc.gif', 'enable'),
			(170, '*BUBA*', 'gd.gif', 'enable'),
			(171, '*RUSSIAN_RU*', 'ge.gif', 'enable'),
			(172, '*BRUNETTE*', 'gf.gif', 'enable'),
			(173, '*GIRL_DEVIL*', 'gg.gif', 'enable'),
			(174, '*GIRL_WEREWOLF*', 'gh.gif', 'enable'),
			(175, '*QUEEN*', 'gi.gif', 'enable'),
			(176, '*KING*', 'gj.gif', 'enable'),
			(177, '*BEACH*', 'gk.gif', 'enable'),
			(178, '*SMOKE*', 'gl.gif', 'enable'),
			(179, '*SCENIC*', 'gm.gif', 'enable'),
			(180, '*READER*', 'gn.gif', 'enable'),
			(181, '*READ*', 'go.gif', 'enable'),
			(182, '*RTFM*', 'gp.gif', 'enable'),
			(183, '*TO_KEEP_ORDER*', 'gq.gif', 'enable'),
			(184, '*WIZARD*', 'gr.gif', 'enable'),
			(185, '*LAZY*', 'gs.gif', 'enable'),
			(186, '*DENTAL*', 'gt.gif', 'enable'),
			(187, '*SUPERSTITION*', 'gu.gif', 'enable'),
			(188, '*CRAZY_PILOT*', 'gv.gif', 'enable'),
			(189, '*TO_BECOME_SENILE*', 'gw.gif', 'enable'),
			(190, '*DOWNLOAD*', 'gx.gif', 'enable'),
			(191, '*TELEPHONE*', 'gy.gif', 'enable'),
			(192, '*DIVER*', 'gz.gif', 'enable'),
			(193, '*WAKE_UP*', 'ha.gif', 'enable'),
			(194, '*ICE_CREAM*', 'hb.gif', 'enable'),
			(195, '*JOURNALIST*', 'hc.gif', 'enable'),
			(196, '*SOAP_BUBBLES*', 'hd.gif', 'enable'),
			(197, '*BODY_BUILDER*', 'he.gif', 'enable'),
			(198, '*CUP_OF_COFFEE*', 'hf.gif', 'enable'),
			(199, '*SOCCER*', 'hg.gif', 'enable'),
			(200, '*SWIMMER*', 'hh.gif', 'enable'),
			(201, '*PIRATE*', 'hi.gif', 'enable'),
			(202, '*CLOWN*', 'hj.gif', 'enable'),
			(203, '*JESTER*', 'hk.gif', 'enable'),
			(204, '*CANNIBAL_DRUMS*', 'hl.gif', 'enable'),
			(205, '*PIONEER*', 'hm.gif', 'enable'),
			(206, '*MOIL*', 'hn.gif', 'enable'),
			(207, '*PAINT*', 'ho.gif', 'enable'),
			(208, '*SUPERMAN*', 'hp.gif', 'enable'),
			(209, '*COLD*', 'hq.gif', 'enable'),
			(210, '*ILLNESS*', 'hr.gif', 'enable'),
			(211, '*WINNER*', 'hs.gif', 'enable'),
			(212, '*POLICE*', 'ht.gif', 'enable'),
			(213, '*TOILET_PLUMS*', 'hu.gif', 'enable'),
			(214, '*DEATH*', 'hv.gif', 'enable'),
			(215, '*ZOMBIE*', 'hw.gif', 'enable'),
			(216, '*UFO*', 'hx.gif', 'enable'),
			(217, '*SUN*', 'hy.gif', 'enable'),
			(218, '*PUMPKIN_GRIEF*', 'hz.gif', 'enable'),
			(219, '*PUMPKIN_SMILE*', 'ia.gif', 'enable'),
			(220, '*POOH_GO*', 'ib.gif', 'enable'),
			(221, '*SEX_BEHIND2*', 'ic.gif', 'enable')
		");

		if (!class_exists('smiles')) a_import('modules/smiles/helpers/smiles');
		smiles::smiles_update($db);
	}

	/**
	 * Деинсталляция модуля
	 */
	public static function uninstall($db) {
		$db->query("DROP TABLE #__smiles;");
	}
}
?>