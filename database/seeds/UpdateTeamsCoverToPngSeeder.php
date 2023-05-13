<?php

use App\Models\Team;
use Illuminate\Database\Seeder;

class UpdateTeamsCoverToPngSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // parse_id => $imageName
        $images = [
            66 => 'manchester-united-10641.png',
            394 => 'huddersfield_town.png',
            74 => 'west_bromwich_albion.png',
            346 => 'watford_fc_logo.png',
            65 => 'manchester.png',
            64 => 'liverpool-logo.png',
            340 => 'southampton.png',
            62 => 'everton.png',
            338 => 'leicester.png',
            73 => 'tottenham-hotspur.png',
            57 => 'arsenal_fc.png',
            61 => 'chelsea_fc.png',
            328 => 'burnley_fc.png',
            70 => 'stoke_city_fc.png',
            72 => 'swansea.png',
            67 => 'newcastle-united.png',
            1044 => 'afc-bournemouth.png',
            354 => 'crystal_palace_fc.png',
            397 => 'brighton-and-hove-albion-fc.png',
            563 => 'west_ham_united_fc.png',
            524 => 'paris_saint-germain.png',
            548 => 'as-monaco-fc-hd.png',
            527 => 'as_saint_etienne.png',
            523 => 'olympique_lyonnais.png',
            516 => 'olympique-de-marseille.png',
            526 => 'fc_girondins_de_bordeaux.png',
            532 => 'angers_sco.png',
            531 => 'es_troyes_ac.png',
            518 => 'montpellier.png',
            576 => 'rc_strasbourg_alsace.png',
            522 => 'ogc_nice.png',
            514 => 'sm_caen.png',
            521 => 'osc_lille.png',
            538 => 'ea_guingamp.png',
            543 => 'fc_nantes.png',
            511 => 'toulouse_fc.png',
            529 => 'stade_rennais_fc.png',
            528 => 'dijon_fco.png',
            545 => 'fc_metz.png',
            530 => 'amiens-sc.png',
            4 => 'borussia_dortmund.png',
            5 => 'bayern.png',
            9 => 'hertha_bsc.png',
            6 => 'fc_schalke.png',
            18 => 'bor_monchengladbach.png',
            7 => 'hamburger_sv.png',
            2 => 'tsg_hoffenheim.png',
            8 => 'hannover_96.png',
            17 => 'logo_freiburg.png',
            19 => 'eintracht-frankfurt-logo.png',
            1 => 'fc_koln.png',
            16 => 'fc-augsburg.png',
            12 => 'werder-leberslang-soccer.png',
            15 => 'fsv_mainz.png',
            3 => 'bayer-leverkusen.png',
            10 => 'logo_stuttgart.png',
            721 => 'red-bull-leipzig.png',
            11 => 'vfl_wolfsburg_old.png',
            86 => 'real-madrid_cf.png',
            81 => 'fc-barcelone.png',
            92 => 'real_sociedad_san_sebastian.png',
            278 => 'sd_eibar.png',
            88 => 'thumb_2022_default_article_fallback.png',
            745 => 'cd_leganes.png',
            95 => 'valence.png',
            298 => 'girona_fc.png',
            78 => 'atletico-madrid.png',
            559 => 'fc-seville.png',
            80 => 'espanyol-barcelone.png',
            77 => 'chac-logo.png',
            82 => 'getafe_grande.png',
            558 => 'sporting-gijon-logo.png',
            84 => 'malaga_cf.png',
            94 => 'villarreal.png',
            263 => 'alaves.png',
            275 => 'las-palmas.png',
            90 => 'real-betis.png',
            560 => 'deportivo-la-coruna-logo.png',
            1779 => 'corinthians.png',
            1767 => 'gremio.png',
            1774 => 'SantosFC.png',
            1769 => 'Palmeiras.png',
            1771 => 'cruzeiro.png',
            1783 => 'Flamengo.png',
            1770 => 'Botafogo.png',
            1780 => 'VascodaGama.png',
            1766 => 'AtleticoMineiro.png',
            1772 => 'Chapecoense.png',
            1777 => 'Bahia.png',
            1776 => 'SaoPaulo.png',
            1768 => 'Clube_Atletico.png',
            1765 => 'fluminense.png',
            1782 => 'Vitoria.png',
            1773 => 'Coritiba.png',
            1778 => 'escudo.png',
            1775 => 'avai.png',
            1781 => 'ponte_preta.png',
            1764 => 'Acg.png',
        ];

        $coverPath = env('APP_URL') . '/storage/teams/';
        foreach ($images as $parseId => $imageName) {
            $model = Team::query()->where('parse_id', $parseId)->first();
            if (!$model) {
                continue;
            }
            // update model cover value
            $model->cover = $coverPath . $imageName;
            // save model to db
            $model->save();
        }
    }
}
