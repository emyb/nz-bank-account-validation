<?php

namespace Glutnix;

class BankAccountValidator
{

    public $bankId = 0;
    public $bankBranch = 0;
    public $bankAccount = 0;
    public $bankAccountSuffix = 0;

    private $bankData = [];
    private $algorithms = [];

    public function __construct($input = null)
    {
        $this->initializeBankData();
        $this->initializeChecksumAlgorithms();
        $this->parseBankAccount($input);
    }

    private function initializeBankData()
    {
        $this->bankData = [
            // Data from pages 89 and 90 of
            // https://www.ird.govt.nz/-/media/project/ir/home/documents/digital-service-providers/software-providers/payroll-calculations-business-rules-specifications/payroll-calculations-and-business-rules-specification-2024-v1-1.pdf
            1 => [
                'algorithm' => 'AB',
                'branches' => [
                    [1, 1], [4, 4], [8, 9], [11, 11], [30, 30], [34, 34], [42, 42], [50, 50], [53, 53], [55, 55],
                    [58, 58], [66, 66], [69, 71], [75, 75], [77, 80], [83, 85], [88, 88], [91, 92], [102, 102],
                    [107, 107], [113, 113], [121, 121], [125, 126], [129, 129], [137, 137], [141, 143], [147, 147],
                    [154, 154], [161, 161], [165, 165], [170, 171], [178, 178], [182, 183], [186, 186], [190, 190],
                    [194, 194], [202, 202], [204, 205], [210, 210], [215, 215], [218, 218], [221, 221], [226, 226],
                    [234, 236], [242, 242], [244, 244], [249, 249], [258, 258], [262, 262], [270, 270], [274, 274],
                    [277, 277], [281, 281], [286, 286], [288, 288], [295, 295], [297, 298], [302, 302], [307, 307],
                    [310, 311], [315, 315], [321, 322], [325, 325], [327, 327], [330, 331], [338, 338], [349, 349],
                    [353, 354], [362, 362], [367, 367], [370, 370], [373, 373], [381, 382], [387, 387], [391, 391],
                    [395, 395], [398, 398], [403, 403], [414, 414], [422, 422], [425, 425], [427, 427], [434, 434],
                    [438, 439], [447, 447], [450, 451], [455, 455], [461, 461], [467, 467], [475, 475], [482, 482],
                    [486, 487], [495, 495], [504, 505], [509, 509], [514, 514], [517, 517], [519, 519], [527, 527],
                    [530, 530], [533, 533], [535, 535], [537, 537], [542, 542], [546, 546], [553, 553], [557, 557],
                    [564, 564], [586, 586], [598, 598], [607, 607], [611, 611], [623, 623], [625, 625], [635, 635],
                    [641, 641], [646, 646], [650, 651], [653, 653], [662, 662], [664, 664], [666, 666], [671, 671],
                    [676, 678], [681, 682], [685, 685], [691, 691], [695, 695], [697, 697], [702, 702], [707, 707],
                    [721, 721], [723, 723], [731, 731], [735, 735], [745, 745], [748, 748], [753, 755], [759, 759],
                    [761, 761], [763, 763], [769, 771], [777, 778], [782, 782], [787, 787], [790, 790], [795, 795],
                    [797, 798], [804, 804], [806, 806], [811, 811], [815, 815], [819, 819], [822, 822], [825, 825],
                    [833, 834], [841, 841], [843, 843], [853, 853], [867, 867], [877, 877], [885, 886], [893, 893],
                    [902, 902], [906, 907], [913, 914], [926, 926], [961, 961], [963, 964], [979, 979], [981, 981],
                    [1101, 1101], [1103, 1125], [1128, 1153], [1155, 1195], [1197, 1199], [1800, 1809],
                    [1811, 1854], [1888, 1889], [6150, 6150],
                ]
            ],
            2 => [
                'algorithm' => 'AB',
                'branches' => [
                    [18, 18], [40, 40], [100, 100], [108, 108], [110, 110], [112, 112], [120, 120], [124, 124],
                    [128, 128], [130, 130], [135, 136], [139, 139], [144, 144], [148, 148], [151, 152], [157, 157],
                    [159, 160], [167, 168], [176, 176], [184, 184], [191, 192], [200, 200], [208, 208], [213, 214],
                    [216, 216], [223, 224], [232, 232], [238, 238], [240, 240], [248, 248], [256, 256], [261, 261],
                    [264, 264], [271, 272], [278, 278], [280, 280], [290, 290], [300, 300], [304, 304], [308, 308],
                    [312, 312], [316, 316], [320, 320], [324, 324], [328, 328], [332, 332], [336, 336], [340, 343],
                    [348, 348], [352, 352], [358, 358], [360, 360], [364, 364], [368, 368], [372, 372], [376, 376],
                    [378, 378], [380, 380], [386, 386], [388, 388], [390, 390], [392, 392], [396, 396], [400, 400],
                    [404, 404], [408, 408], [410, 410], [412, 412], [416, 416], [424, 424], [428, 428], [432, 432],
                    [436, 436], [440, 440], [444, 444], [448, 448], [452, 452], [454, 454], [456, 456], [464, 464],
                    [466, 466], [468, 468], [470, 470], [472, 472], [476, 476], [478, 478], [480, 480], [484, 484],
                    [488, 488], [492, 492], [494, 494], [496, 496], [499, 500], [506, 506], [512, 512], [520, 520],
                    [524, 524], [528, 528], [534, 534], [536, 536], [540, 540], [544, 544], [548, 548], [551, 552],
                    [554, 555], [560, 560], [562, 563], [568, 568], [570, 570], [573, 573], [576, 576], [585, 585],
                    [590, 591], [600, 600], [602, 602], [604, 604], [608, 608], [610, 610], [612, 612], [620, 620],
                    [624, 624], [628, 628], [630, 630], [632, 632], [636, 636], [640, 640], [644, 644], [648, 648],
                    [652, 652], [655, 656], [659, 659], [668, 668], [672, 673], [680, 680], [684, 684], [686, 686],
                    [688, 688], [692, 692], [700, 700], [704, 704], [708, 708], [712, 712], [716, 716], [719, 720],
                    [724, 724], [727, 727], [733, 733], [740, 741], [747, 747], [756, 756], [760, 760], [764, 764],
                    [766, 766], [772, 772], [776, 776], [780, 780], [784, 784], [788, 788], [792, 792], [796, 796],
                    [800, 800], [808, 808], [810, 810], [816, 816], [820, 820], [828, 828], [832, 832], [836, 836],
                    [840, 840], [842, 842], [844, 844], [848, 848], [852, 852], [856, 856], [858, 858], [860, 860],
                    [863, 865], [868, 868], [871, 871], [874, 876], [880, 880], [884, 884], [888, 888], [892, 892],
                    [896, 896], [900, 900], [908, 908], [910, 910], [912, 912], [916, 916], [918, 918], [920, 920],
                    [922, 922], [924, 924], [929, 930], [935, 935], [938, 938], [940, 940], [944, 944], [946, 946],
                    [948, 948], [950, 950], [953, 953], [957, 957], [959, 959], [965, 965], [975, 975], [985, 985],
                    [987, 987], [989, 989], [993, 993], [995, 995], [1201, 1204], [1206, 1207], [1209, 1223],
                    [1225, 1269], [1271, 1276], [1278, 1278], [1280, 1281], [1283, 1283], [1285, 1286],
                    [1288, 1292], [1294, 1298], [2025, 2055],
                ]
            ],
            3 => [
                'algorithm' => 'AB',
                'branches' => [
                    [31, 32], [43, 44], [47, 49], [59, 60], [62, 62], [65, 65], [72, 72], [99, 99], [104, 105],
                    [109, 109], [114, 114], [116, 116], [118, 118], [123, 123], [127, 127], [132, 133], [138, 138],
                    [140, 140], [146, 146], [149, 150], [155, 156], [162, 162], [166, 166], [173, 175], [179, 181],
                    [187, 187], [189, 189], [195, 196], [198, 198], [203, 203], [206, 207], [211, 212], [219, 220],
                    [227, 228], [231, 231], [239, 239], [243, 243], [250, 253], [255, 255], [259, 259], [263, 263],
                    [267, 269], [275, 276], [282, 283], [285, 285], [291, 292], [296, 296], [303, 303], [305, 306],
                    [314, 314], [318, 319], [326, 326], [334, 334], [339, 339], [345, 347], [351, 351], [355, 356],
                    [363, 363], [366, 366], [371, 371], [374, 374], [385, 385], [389, 389], [394, 394], [399, 399],
                    [406, 407], [415, 415], [417, 419], [423, 423], [426, 426], [430, 431], [435, 435], [442, 443],
                    [445, 446], [449, 449], [458, 458], [463, 463], [474, 474], [481, 481], [485, 485], [490, 490],
                    [497, 498], [502, 503], [510, 511], [515, 515], [518, 518], [521, 522], [525, 525], [531, 532],
                    [538, 539], [543, 543], [547, 547], [550, 550], [558, 559], [566, 567], [572, 572], [578, 579],
                    [584, 584], [587, 588], [595, 595], [597, 597], [599, 599], [605, 605], [609, 609], [614, 615],
                    [617, 619], [626, 627], [631, 631], [633, 633], [638, 639], [642, 643], [647, 647], [654, 654],
                    [657, 658], [661, 661], [667, 667], [670, 670], [674, 675], [683, 683], [687, 687], [690, 690],
                    [693, 693], [698, 699], [703, 703], [706, 706], [710, 711], [713, 713], [715, 715], [718, 718],
                    [722, 722], [725, 726], [728, 728], [732, 732], [734, 734], [737, 737], [739, 739], [742, 742],
                    [749, 751], [758, 758], [762, 762], [767, 767], [774, 775], [779, 779], [783, 783], [785, 786],
                    [791, 791], [794, 794], [799, 799], [802, 803], [809, 809], [813, 814], [818, 818], [823, 824],
                    [826, 827], [830, 830], [835, 835], [838, 839], [846, 847], [850, 850], [854, 855], [857, 857],
                    [859, 859], [861, 861], [866, 866], [873, 873], [881, 881], [883, 883], [887, 887], [890, 890],
                    [895, 895], [897, 898], [903, 905], [915, 915], [931, 931], [933, 934], [937, 937], [947, 947],
                    [951, 951], [960, 960], [962, 962], [978, 978], [990, 990], [1300, 1335], [1340, 1342],
                    [1350, 1372], [1382, 1399], [1500, 1510], [1512, 1540], [1544, 1553], [1555, 1568],
                    [1570, 1578],[1582, 1588], [1590, 1599], [1700, 1707], [1709, 1711], [1714, 1720], [1725, 1730],
                    [1732, 1739], [1742, 1799], [1900, 1919], [7355, 7355],
                ]
            ],
            4 => [
                'algorithm' => 'AB',
                'branches' => [
                    [2014, 2024],
                ]
            ],
            5 => [
                'algorithm' => 'AB',
                'branches' => [
                    [8884, 8889]
                ]
            ],
            6 => [
                'algorithm' => 'AB',
                'branches' => [
                    [6, 6], [23, 24], [67, 67], [81, 82], [101, 101], [103, 103], [111, 111], [115, 115],
                    [122, 122], [134, 134], [145, 145], [153, 153], [158, 158], [163, 164], [169, 169], [172, 172],
                    [177, 177], [185, 185], [188, 188], [193, 193], [197, 197], [199, 199], [201, 201], [209, 209],
                    [217, 217], [222, 222], [225, 225], [229, 230], [233, 233], [237, 237], [241, 241], [254, 254],
                    [257, 257], [265, 266], [273, 273], [284, 284], [287, 287], [293, 294], [299, 299], [301, 301],
                    [309, 309], [313, 313], [317, 317], [323, 323], [329, 329], [333, 333], [335, 335], [337, 337],
                    [350, 350], [359, 359], [361, 361], [365, 365], [369, 369], [375, 375], [377, 377], [379, 379],
                    [383, 383], [393, 393], [397, 397], [401, 401], [405, 405], [409, 409], [411, 411], [413, 413],
                    [421, 421], [429, 429], [433, 433], [437, 437], [441, 441], [453, 453], [457, 457], [459, 459],
                    [465, 465], [469, 469], [471, 471], [473, 473], [477, 477], [479, 479], [483, 483], [489, 489],
                    [491, 491], [493, 493], [501, 501], [507, 507], [513, 513], [529, 529], [541, 541], [545, 545],
                    [549, 549], [556, 556], [561, 561], [565, 565], [569, 569], [574, 575], [577, 577], [580, 583],
                    [589, 589], [592, 592], [594, 594], [596, 596], [601, 601], [603, 603], [606, 606], [613, 613],
                    [622, 622], [629, 629], [637, 637], [645, 645], [649, 649], [663, 663], [665, 665], [669, 669],
                    [689, 689], [701, 701], [705, 705], [709, 709], [729, 730], [738, 738], [746, 746], [757, 757],
                    [765, 765], [773, 773], [781, 781], [789, 789], [793, 793], [801, 801], [805, 805], [807, 807],
                    [817, 817], [821, 821], [829, 829], [831, 831], [837, 837], [845, 845], [849, 849], [851, 851],
                    [869, 870], [878, 879], [889, 889], [894, 894], [899, 899], [901, 901], [909, 909], [911, 911],
                    [917, 917], [919, 919], [921, 921], [923, 923], [925, 925], [927, 927], [936, 936], [939, 939],
                    [941, 943], [949, 949], [954, 954], [956, 956], [958, 958], [966, 966], [968, 968], [977, 977],
                    [983, 983], [986, 986], [991, 991], [994, 994], [996, 996], [998, 998], [1458, 1458],
                    [1499, 1499],
                ]
            ],
            8 => [
                'algorithm' => 'D',
                'branches' => [
                    [6501, 6504], [6511, 6511], [6513, 6515], [6517, 6517], [6519, 6519], [6521, 6521],
                    [6523, 6523], [6525, 6525], [6529, 6529], [6531, 6531], [6533, 6533], [6535, 6535],
                    [6537, 6537], [6541, 6541], [6543, 6543], [6551, 6551], [6553, 6553], [6555, 6555],
                    [6557, 6557], [6559, 6559], [6561, 6561], [6563, 6563], [6567, 6567], [6571, 6571],
                    [6573, 6573], [6575, 6575], [6581, 6581], [6583, 6583], [6585, 6585], [6587, 6587],
                    [6589, 6589], [6593, 6593], [6597, 6597], [6599, 6599],
                ]
            ],
            10 => [
                'algorithm' => 'AB',
                'branches' => [
                    [5165, 5169],
                ]
            ],
            11 => [
                'algorithm' => 'AB',
                'branches' => [
                    [5000, 5000], [5017, 5017], [5026, 5027], [5029, 5029], [5031, 5033], [5146, 5147],
                    [5200, 5203], [5211, 5211], [5216, 5216], [5220, 5220], [5228, 5228], [5230, 5230],
                    [5234, 5234], [5242, 5242], [5249, 5250], [5253, 5254], [5264, 5265], [5267, 5267],
                    [5274, 5277], [5284, 5284], [5299, 5299], [5301, 5301], [5313, 5314], [5316, 5316],
                    [5318, 5318], [5332, 5332], [5337, 5339], [5344, 5346], [5350, 5351], [5358, 5358],
                    [5360, 5360], [5362, 5362], [5369, 5369], [5372, 5373], [5377, 5377], [5389, 5389],
                    [5392, 5392], [5397, 5397], [5400, 5402], [5407, 5407], [5409, 5409], [5420, 5420],
                    [5422, 5422], [5432, 5432], [5434, 5434], [5438, 5438], [5441, 5441], [5443, 5443],
                    [5448, 5448], [5458, 5458], [5460, 5460], [5462, 5463], [5468, 5468], [5495, 5495],
                    [5508, 5515], [5527, 5527], [5700, 5700], [5704, 5704], [5712, 5712], [5715, 5715],
                    [5719, 5720], [5722, 5722], [5724, 5725], [5727, 5727], [5731, 5731], [5736, 5736],
                    [5751, 5751], [5755, 5755], [5760, 5760], [5762, 5762], [5765, 5765], [5773, 5773],
                    [5785, 5785], [5789, 5789], [5804, 5804], [5809, 5809], [5814, 5814], [5827, 5827],
                    [5832, 5832], [5849, 5849], [5852, 5853], [5859, 5859], [5900, 5900], [5925, 5925],
                    [5931, 5931], [5934, 5934], [5941, 5941], [5943, 5943], [6000, 6001], [6010, 6010],
                    [6013, 6013], [6015, 6015], [6017, 6017], [6022, 6022], [6030, 6031], [6100, 6100],
                    [6102, 6102], [6115, 6115], [6123, 6123], [6147, 6147], [6163, 6163], [6168, 6168],
                    [6183, 6183], [6187, 6187], [6189, 6189], [6300, 6300], [6310, 6310], [6347, 6347],
                    [6400, 6401], [6421, 6422], [6424, 6424], [6432, 6432], [6439, 6439], [6459, 6460],
                    [6462, 6462], [6477, 6479], [6600, 6601], [6620, 6621], [6623, 6623], [6627, 6627],
                    [6629, 6629], [6634, 6634], [6654, 6654], [6660, 6660], [6676, 6676], [6800, 6800],
                    [6820, 6820], [6833, 6833], [6836, 6836], [6840, 6841], [6847, 6847], [6849, 6849],
                    [6855, 6855], [6862, 6862], [6900, 6903], [6912, 6912], [6916, 6917], [6919, 6919],
                    [6932, 6932], [6952, 6952], [6962, 6962], [6965, 6965], [6972, 6972], [6974, 6974],
                    [7000, 7001], [7026, 7026], [7100, 7100], [7114, 7114], [7116, 7117], [7119, 7119],
                    [7123, 7123], [7200, 7200], [7202, 7203], [7216, 7216], [7219, 7221], [7231, 7231],
                    [7234, 7234], [7239, 7239], [7244, 7244], [7247, 7247], [7249, 7251], [7255, 7255],
                    [7259, 7260], [7265, 7265], [7267, 7267], [7278, 7278], [7281, 7284], [7286, 7287],
                    [7290, 7290], [7292, 7292], [7300, 7300], [7302, 7302], [7309, 7309], [7311, 7311],
                    [7313, 7314], [7318, 7320], [7328, 7329], [7340, 7349], [7400, 7400], [7402, 7402],
                    [7426, 7426], [7428, 7428], [7436, 7436], [7438, 7438], [7443, 7443], [7446, 7446],
                    [7500, 7500], [7517, 7517], [7528, 7528], [7532, 7532], [7600, 7600], [7626, 7626],
                    [7653, 7653], [7700, 7700], [7800, 7800], [7802, 7803], [7810, 7811], [7814, 7814],
                    [7816, 7816], [7820, 7820], [7824, 7824], [7848, 7848], [7850, 7850], [7854, 7854],
                    [7856, 7856], [7860, 7860], [7865, 7865], [7870, 7870], [7876, 7876], [7881, 7881],
                    [7885, 7885], [7887, 7889], [7892, 7893], [7901, 7901], [7909, 7909], [7911, 7911],
                    [7916, 7916], [7920, 7920], [7931, 7932], [7947, 7947], [7956, 7956], [8000, 8001],
                    [8003, 8005], [8007, 8008], [8013, 8013], [8100, 8100], [8102, 8102], [8105, 8105],
                    [8116, 8117], [8120, 8120], [8138, 8138], [8144, 8145], [8147, 8147], [8200, 8200],
                    [8300, 8300], [8310, 8310], [8313, 8314], [8322, 8322], [8327, 8327], [8330, 8330],
                    [8337, 8339], [8365, 8365], [8367, 8367], [8378, 8378], [8381, 8381], [8386, 8387],
                    [8390, 8390], [8393, 8393], [8406, 8406], [8422, 8422], [8425, 8429], [8431, 8431],
                    [8500, 8500], [8503, 8503], [8505, 8505], [8515, 8515], [8530, 8530], [8532, 8532],
                    [8537, 8537], [8552, 8552], [8556, 8556], [8564, 8564], [8570, 8570], [8572, 8572],
                    [8576, 8576], [8593, 8593], [8750, 8750], [8760, 8760], [8770, 8770], [8780, 8780],
                    [8991, 8991], [8994, 8997], [8999, 8999],
                ]
            ],
            12 => [
                'algorithm' => 'AB',
                'branches' => [
                    [3001, 3003], [3006, 3132], [3134, 3155], [3157, 3201], [3205, 3205], [3207, 3282],
                    [3284, 3290], [3292, 3292], [3295, 3296], [3400, 3443], [3445, 3462], [3474, 3486],
                    [3488, 3499], [3601, 3607], [3610, 3620], [3622, 3651], [3653, 3659], [3661, 3676],
                    [3678, 3678], [3680, 3680],
                ]
            ],
            13 => [
                'algorithm' => 'AB',
                'branches' => [
                    [4901, 4919], [4926, 4930],
                ]
            ],
            14 => [
                'algorithm' => 'AB',
                'branches' => [
                    [4701, 4703], [4705, 4705], [4707, 4707], [4711, 4711], [4713, 4713], [4715, 4715],
                    [4717, 4717], [4719, 4719], [4723, 4723], [4725, 4725], [4727, 4727], [4729, 4729],
                    [4733, 4733], [4735, 4735], [4737, 4737], [4739, 4739], [4741, 4741], [4761, 4761],
                    [4763, 4763], [4765, 4765], [4767, 4767], [4769, 4769], [4773, 4773], [4775, 4775],
                    [4777, 4777], [4779, 4779], [4781, 4781], [4783, 4783], [4795, 4796], [4798, 4799],
                ]
            ],
            15 => [
                'algorithm' => 'AB',
                'branches' => [
                    [3941, 3960], [3968, 3979], [3981, 3981], [3987, 3988],
                ]
            ],
            16 => [
                'algorithm' => 'AB',
                'branches' => [
                    [4402, 4409], [4412, 4414], [4416, 4417], [4423, 4423], [4425, 4425], [4428, 4428],
                    [4430, 4432], [4434, 4439], [4441, 4441], [4443, 4443], [4445, 4456], [4458, 4458],
                    [4460, 4461], [4463, 4468], [4470, 4470], [4472, 4473], [4475, 4477], [4479, 4488],
                    [4490, 4490], [4492, 4493], [4496, 4498],
                ]
            ],
            17 => [
                'algorithm' => 'AB',
                'branches' => [
                    [3331, 3332], [3360, 3393], [3395, 3396], [3399, 3399],
                ]
            ],
            18 => [
                'algorithm' => 'AB',
                'branches' => [
                    [3501, 3527], [3530, 3530], [3550, 3550], [3589, 3590],
                ]
            ],
            19 => [
                'algorithm' => 'AB',
                'branches' => [
                    [4617, 4622], [4624, 4624], [4626, 4627], [4629, 4629], [4631, 4631], [4633, 4633],
                    [4635, 4635], [4647, 4648],
                ]
            ],
            20 => [
                'algorithm' => 'AB',
                'branches' => [
                    [4121, 4141], [4143, 4147], [4169, 4170], [4198, 4198],
                ]
            ],
            21 => [
                'algorithm' => 'AB',
                'branches' => [
                    [4801, 4831], [4833, 4833], [4895, 4895], [4897, 4899],
                ]
            ],
            22 => [
                'algorithm' => 'AB',
                'branches' => [
                    [4000, 4000], [4002, 4009], [4021, 4025], [4028, 4028], [4030, 4033], [4049, 4049],
                ]
            ],
            23 => [
                'algorithm' => 'AB',
                'branches' => [
                    [3700, 3704], [3712, 3712], [3714, 3714], [3716, 3716], [3718, 3718], [3720, 3720],
                    [3724, 3724], [3730, 3730], [3732, 3732], [3734, 3734], [3736, 3736], [3738, 3738],
                    [3746, 3746], [3748, 3748], [3750, 3750], [3754, 3754], [3756, 3758], [3760, 3760],
                    [3762, 3762], [3765, 3765], [3770, 3770], [3772, 3772], [3780, 3780], [3784, 3784],
                    [3786, 3788], [3792, 3794], [3798, 3798],
                ]
            ],
            24 => [
                'algorithm' => 'AB',
                'branches' => [
                    [4310, 4312], [4315, 4316], [4319, 4321], [4330, 4330], [4335, 4338], [4340, 4340],
                ]
            ],
            25 => [
                'algorithm' => 'F',
                'branches' => [
                    [2500, 2503], [2509, 2510], [2520, 2529], [2531, 2570],
                ]
            ],
            27 => [
                'algorithm' => 'AB',
                'branches' => [
                    [3801, 3803], [3805, 3805], [3816, 3818], [3820, 3826],
                ]
            ],
            30 => [
                'algorithm' => 'AB',
                'branches' => [
                    [2901, 2904], [2906, 2909], [2911, 2912], [2916, 2916], [2922, 2922], [2932, 2932],
                    [2937, 2937], [2940, 2940], [2948, 2948],
                ]
            ],
            31 => [
                'algorithm' => 'X',
                'branches' => [
                    [2825, 2829], [2840, 2840],
                ]
            ],
            38 => [
                'algorithm' => 'AB',
                'branches' => [
                    [9000, 9499],
                ]
            ],
            88 => [
                'algorithm' => 'AB',
                'branches' => [
                    [8800, 8803], [8805],
                ]
            ],
        ];
    }

    private function initializeChecksumAlgorithms()
    {
        $this->algorithms = [
            'A' => ['weightDigits' => [0, 0,  6, 3, 7, 9,  0, 0, 10, 5, 8, 4, 2, 1,  0, 0, 0, 0], 'modulo' => 11],
            'B' => ['weightDigits' => [0, 0,  0, 0, 0, 0,  0, 0, 10, 5, 8, 4, 2, 1,  0, 0, 0, 0], 'modulo' => 11],
            'C' => ['weightDigits' => [3, 7,  0, 0, 0, 0,  9, 1, 10, 5, 3, 4, 2, 1,  0, 0, 0, 0], 'modulo' => 11],
            'D' => ['weightDigits' => [0, 0,  0, 0, 0, 0,  0, 7, 6,  5, 4, 3, 2, 1,  0, 0, 0, 0], 'modulo' => 11],
            'E' => ['weightDigits' => [0, 0,  0, 0, 0, 0,  0, 0, 0,  0, 5, 4, 3, 2,  0, 0, 0, 1], 'modulo' => 11],
            'F' => ['weightDigits' => [0, 0,  0, 0, 0, 0,  0, 1, 7,  3, 1, 7, 3, 1,  0, 0, 0, 0], 'modulo' => 10],
            'G' => ['weightDigits' => [0, 0,  0, 0, 0, 0,  0, 1, 3,  7, 1, 3, 7, 1,  0, 3, 7, 1], 'modulo' => 10],
            'X' => ['weightDigits' => [0, 0,  0, 0, 0, 0,  0, 0, 0,  0, 0, 0, 0, 0,  0, 0, 0, 0], 'modulo' => 1],
        ];
    }

    public function parseBankAccount($input)
    {
        if (is_string($input)) {
            $this->parseBankAccountString($input);
        } elseif (is_array($input)) {
            $this->parseBankAccountArray($input);
        } else {
            throw new \InvalidArgumentException("Expected a string, or an array of either four strings or integers.");
        }
    }

    private function parseBankAccountArray($parts)
    {
        $this->bankId = (int)$parts[0];
        $this->bankBranch = (int)$parts[1];
        $this->bankAccount = (int)$parts[2];
        $this->bankAccountSuffix = (int)$parts[3];
    }

    private function parseBankAccountString($string)
    {
        $parts = preg_split("/[\s-]+/", $string);
        if (count($parts) !== 4) {
            throw new \InvalidArgumentException("Could not break bank account string into exactly four parts.
             Make sure you separate the parts using spaces or minuses");
        }
        $this->parseBankAccountArray($parts);
    }

    public function bankIdIsValid()
    {
        return array_key_exists($this->bankId, $this->bankData);
    }

    public function bankBranchIsValid()
    {
        if (! $this->bankIdIsValid()) {
            return false;
        }
        $branches = $this->getValidBankBranches();
        foreach ($branches as $branchrange) {
            if ($this->bankBranch >= $branchrange[0] && $this->bankBranch <= $branchrange[1]) {
                return true;
            }
        }
        return false;
    }

    public function bankAccountIsValid()
    {
        return ($this->bankAccount > 0 && $this->bankAccount <= 99999999);
    }

    private function getValidBankBranches()
    {
        return $this->bankData[$this->bankId]['branches'];
    }

    public function isValid()
    {
        if (! $this->bankIdIsValid()) {
            return false;
        }
        if (! $this->bankBranchIsValid()) {
            return false;
        }
        return $this->checksumIsValid();
    }

    private function getBankIdAlgorithm()
    {
        $code = $this->bankData[$this->bankId]['algorithm'];
        if ($code == "AB") {
            if ($this->bankAccount < 990000) {
                return 'A';
            } else {
                return 'B';
            }
        }
        return $code;
    }

    private function getBankIdWeightDigits()
    {
        return $this->algorithms[$this->getBankIdAlgorithm()];
    }

    private function checksumIsValid()
    {
        $account = $this->getAccountAsDigitArray();
        $algorithm = $this->getBankIdWeightDigits();
        $sum = 0;
        foreach ($account as $index => $digit) {
            $sum += ($account[$index] * $algorithm['weightDigits'][$index]);
        }
        return $sum % $algorithm['modulo'] === 0;
    }

    public function getAccountAsDigitArray()
    {
        extract($this->getAccountPartsAsStrings());
        $digits = $bankId . $bankBranch . $bankAccount . $bankAccountSuffix;
        return str_split($digits, 1);
    }

    public function getAccountAsString(
        $seperator = "-",
        $bankIdPadding = 2,
        $bankBranchPadding = 4,
        $bankAccountPadding = 8,
        $bankAccountSuffix = 4
    ) {
        extract($this->getAccountPartsAsStrings(
            $bankIdPadding,
            $bankBranchPadding,
            $bankAccountPadding,
            $bankAccountSuffix
        ));
        $account = $bankId . $seperator
                . $bankBranch . $seperator
                . $bankAccount . $seperator
                . $bankAccountSuffix;
        return $account;
    }

    public function getAccountPartsAsStrings(
        $bankIdPadding = 2,
        $bankBranchPadding = 4,
        $bankAccountPadding = 8,
        $bankAccountSuffixPadding = 4
    ) {
        $bankIdPadding =            min($bankIdPadding, 2);
        $bankBranchPadding =        min($bankBranchPadding, 4);
        $bankAccountPadding =       min($bankAccountPadding, 8);
        $bankAccountSuffixPadding = min($bankAccountSuffixPadding, 4);

        $bankId =               sprintf("%'.0" . $bankIdPadding . "d", $this->bankId);
        $bankBranch =           sprintf("%'.0" . $bankBranchPadding . "d", $this->bankBranch);
        $bankAccount =          sprintf("%'.0" . $bankAccountPadding . "d", $this->bankAccount);
        $bankAccountSuffix =    sprintf("%'.0" . $bankAccountSuffixPadding . "d", $this->bankAccountSuffix);
        return compact('bankId', 'bankBranch', 'bankAccount', 'bankAccountSuffix');
    }
}
